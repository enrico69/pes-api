<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       21/09/2019 (dd-mm-YYYY)
 */

namespace App\Service\Match;

use App\Entity\Event;
use App\Entity\Goal;
use App\Entity\Match;
use App\Model\MatchPayload;
use Doctrine\ORM\EntityManagerInterface;

class MatchUpdateManager
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function update(MatchPayload $matchPayload): void
    {
        $match = $matchPayload->getMatch();

        /*
         * Wondering if a part of the job could simply have been done earlier
         * in the request manager...
         */
        $match->setDate(new \DateTime());
        $match->setStadium($matchPayload->getStadium());
        $match->setAppearances($matchPayload->getAppearances());
        $match->setComment($matchPayload->getComments());
        $match->setEvents($matchPayload->getEvents());
        $match->setGoals($matchPayload->getGoals());

        $this->setScoreData($matchPayload->getGoals(), $matchPayload->getEvents(), $match);
        $this->setPlayersStats(
            $matchPayload->getGoals(),
            $matchPayload->getEvents(),
            $matchPayload->getAppearances(),
            $match
        );

        $this->entityManager->flush();
    }

    /**
     * @param \App\Entity\Goal[]       $goals
     * @param \App\Entity\Event[]      $events
     * @param \App\Entity\Appearance[] $appareances
     * @param \App\Entity\Match        $match
     *
     * @throws \Exception
     */
    private function setPlayersStats(array $goals, array $events, array $appareances, Match $match): void
    {
        // Selections
        foreach ($appareances as $appareance) {
            $appareance->getPlayer()->incrementMatchCount();
            $appareance->getPlayer()->setUpdateDate(new \DateTime());
        }

        // Goals and assists
        foreach ($goals as $goal) {
            if (Goal::TYPE_OWN_GOAL === $goal->getType()) {
                $goal->getPlayer()->incrementOwnGoalCount();

                if ($goal->getTeam()->getId() === $match->getTeam1()->getId()) {
                    $match->getGamer2()->incrementOwnGoalCount();
                    $match->getTeam2()->incrementOwnGoalCount();
                } else {
                    $match->getGamer1()->incrementOwnGoalCount();
                    $match->getTeam1()->incrementOwnGoalCount();
                }
            } else {
                $goal->getPlayer()->incrementGoalCount();
            }

            if ($goal->getAssist()) {
                $goal->getAssist()->incrementAssistCount();
            }

            if ($goal->getTeam()->getId() === $match->getTeam1()->getId()) {
                $match->getTeam1()->incrementGoalScoredCount();
                $match->getGamer1()->incrementGoalScoredCount();
                $match->getTeam2()->incrementGoalReceivedCount();
                $match->getGamer2()->incrementGoalReceivedCount();
            } else {
                $match->getTeam2()->incrementGoalScoredCount();
                $match->getGamer2()->incrementGoalScoredCount();
                $match->getTeam1()->incrementGoalReceivedCount();
                $match->getGamer1()->incrementGoalReceivedCount();
            }
        }

        // Events
        foreach ($events as $event) {
            switch ($event->getType()) {
                case Event::TYPE_WOUND:
                    $event->getPlayer()->incrementInjuryCount();
                    $event->getGamer()->incrementInjuryReceivedCount();
                    $event->getTeam()->incrementInjuryReceivedCount();
                    if ($event->getTeam()->getId() === $match->getTeam1()->getId()) {
                        $match->getTeam2()->incrementInjuryDoneCount();
                        $match->getGamer2()->incrementInjuryDoneCount();
                    } else {
                        $match->getTeam1()->incrementInjuryDoneCount();
                        $match->getGamer1()->incrementInjuryDoneCount();
                    }
                    break;
                case Event::TYPE_RED_CARD:
                    $event->getPlayer()->incrementRedCardCount();
                    $event->getGamer()->incrementRedCardCount();
                    $event->getTeam()->incrementRedCardCount();
                    break;
                case Event::TYPE_YELLOW_CARD:
                    $event->getPlayer()->incrementYellowCardCount();
                    $event->getGamer()->incrementYellowCardCount();
                    $event->getTeam()->incrementYellowCardCount();
                    break;
                default: break; // For now, some are ignored, just saved in the DB.
            }
        }
    }

    /**
     * @param \App\Entity\Goal[]  $goals
     * @param \App\Entity\Event[] $events
     * @param \App\Entity\Match   $match
     */
    private function setScoreData(array $goals, array $events, Match $match): void
    {
        // Shortcut
        $team1 = $match->getTeam1();
        $team2 = $match->getTeam2();

        $scores = [
            'half' => [$team1->getId() => 0, $team2->getId() => 0],
            'full' => [$team1->getId() => 0, $team2->getId() => 0],
            'half-extra' => [$team1->getId() => 0, $team2->getId() => 0],
            'full-extra' => [$team1->getId() => 0, $team2->getId() => 0],
        ];

        foreach ($goals as $goal) {
            $minute = $goal->getScoredAt();
            $minute = explode('+', $minute);
            $minute[0] = (int) $minute[0];
            if (!empty($minute[1])) {
                $minute[1] = (int) $minute[1];
            }

            if ($minute[0] <= 45 || (45 === $minute[0] && !empty($minute[1]))) {
                $part = 'half';
            } elseif ($minute[0] <= 90 || (90 === $minute[0] && !empty($minute[1]))) {
                $part = 'full';
            } elseif ($minute[0] <= 105 || (105 === $minute[0] && !empty($minute[1]))) {
                $part = 'half-extra';
            } else {
                $part = 'full-extra';
            }

            ++$scores[$part][$goal->getTeam()->getId()];
        }

        $match->setTeam1HalfTimeScore($scores['half'][$team1->getId()]);
        $match->setTeam1FullTimeScore($scores['full'][$team1->getId()]);
        $match->setTeam1ProlongHalfTimeScore($scores['half-extra'][$team1->getId()]);
        $match->setTeam1ProlongFullTimeScore($scores['full-extra'][$team1->getId()]);

        // Penalties shootout
        $team1PenScore = 0;
        $team2PenScore = 0;
        foreach ($events as $event) {
            if (Event::PEN_SHOOTOUTS_SCORED_KEY === $event->getType()) {
                if ($event->getTeam()->getId() === $match->getTeam1()->getId()) {
                    ++$team1PenScore;
                } else {
                    ++$team2PenScore;
                }
            }
        }

        $match->setTeam1PenaltyScore($team1PenScore);
        $match->setTeam2PenaltyScore($team2PenScore);

        $team1Total =
            $match->getTeam1HalfTimeScore()
            + $match->getTeam1FullTimeScore()
            + $match->getTeam1ProlongHalfTimeScore()
            + $match->getTeam1ProlongFullTimeScore();

        $match->setTeam2HalfTimeScore($scores['half'][$team2->getId()]);
        $match->setTeam2FullTimeScore($scores['full'][$team2->getId()]);
        $match->setTeam2ProlongHalfTimeScore($scores['half-extra'][$team2->getId()]);
        $match->setTeam2ProlongFullTimeScore($scores['full-extra'][$team2->getId()]);

        $team2Total =
            $match->getTeam2HalfTimeScore()
            + $match->getTeam2FullTimeScore()
            + $match->getTeam2ProlongHalfTimeScore()
            + $match->getTeam2ProlongFullTimeScore();

        if ($team1Total > $team2Total || $team1PenScore > $team2PenScore) {
            $match->setWinnerTeam($team1);
            $match->setWinnerGamer($match->getGamer1());
            $match->getGamer1()->incrementVictoriesCount();
            $match->getGamer2()->incrementDefeatsCount();
            $match->getTeam1()->incrementVictoriesCount();
            $match->getTeam2()->incrementDefeatsCount();
        } elseif ($team1Total < $team2Total || $team1PenScore < $team2PenScore) {
            $match->setWinnerTeam($team2);
            $match->setWinnerGamer($match->getGamer2());
            $match->getGamer2()->incrementVictoriesCount();
            $match->getGamer1()->incrementDefeatsCount();
            $match->getTeam2()->incrementVictoriesCount();
            $match->getTeam1()->incrementDefeatsCount();
        } else {
            // Draw (championship, or cup first round).
            $match->getTeam1()->incrementDrawsCount();
            $match->getTeam2()->incrementDrawsCount();
            $match->getGamer1()->incrementDrawsCount();
            $match->getGamer2()->incrementDrawsCount();
        }
    }
}
