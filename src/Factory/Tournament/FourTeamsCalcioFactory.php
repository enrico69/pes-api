<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       04/09/2019 (dd-mm-YYYY)
 */

namespace App\Factory\Tournament;

use App\Entity\Match;
use App\Entity\Gamer;
use App\Entity\Team;
use App\Model\GamerTeamAssociationCollection;
use App\Model\MatchCollection;

class FourTeamsCalcioFactory
{
    /** @var \App\Model\GamerTeamAssociationCollection */
    private $gamerTeamAssociationCollection;
    /** @var \App\Entity\Team */
    private $winnerTeam;
    private $isSpecial = false;

    /**
     * @param \App\Model\GamerTeamAssociationCollection $gamerTeamAssociationCollection
     * @param \App\Entity\Team|null                     $winnerTeam
     *
     * @return \App\Model\MatchCollection
     */
    public function generate(GamerTeamAssociationCollection $gamerTeamAssociationCollection, ?Team $winnerTeam = null)
    {
        if (!$gamerTeamAssociationCollection->isLocked()) {
            throw new \LogicException('The collection is not locked!');
        }

        if (\count($gamerTeamAssociationCollection) !== 4) {
            throw new \LogicException('The collection does not contain 4 associations!');
        }

        $this->gamerTeamAssociationCollection = $gamerTeamAssociationCollection;
        /**
         * If no winner team (e.g, the team is not present in the tournament, let's take one HUMAN randomly,
         * it will be used to decide the first match of the tournament. We take a human because we never
         * want a computer opponent in the last match.
         * Note that we make a clear difference: the winner is not the gamer (coach), it is the team.
         */
        $this->winnerTeam = $winnerTeam ?? $this->gamerTeamAssociationCollection->getRandomAssociation([], Gamer::TYPE_COMPUTER)->getTeam();


        // Damien is here AND one gamer is managed by the computer.
        if ($gamerTeamAssociationCollection->getComputerCount() === 1
            && $gamerTeamAssociationCollection->getDamienTeamId()) {
            $this->isSpecial = true;
        }

        return $this->createCalcio();
    }

    private function getFirstMatchesTeams() : array
    {
        $exclusions = [$this->winnerTeam->getId()];

        $randomAssoc1 = $this->gamerTeamAssociationCollection->getRandomAssociation($exclusions);
        $firstMatchTeam1 = $this->winnerTeam;
        $firstMatchTeam2 = $randomAssoc1->getTeam();

        return [$firstMatchTeam1, $firstMatchTeam2];
    }

    private function getSpecialCalcioFirstMatchesTeams() : array
    {
        /**
         * J1
         * Remember, the winner (or auto selected winner) is always a team, not controlled by the computer
         * for the reason explained above. And he always plays against a human.
         */
        $damienAssociation = $this->gamerTeamAssociationCollection->getDamienAssociation();

        if ($this->winnerTeam->getId() === $damienAssociation->getTeam()->getId()) {
            $firstMatchTeam1 =  $damienAssociation->getTeam();
            $firstMatchTeam2 =  $this->gamerTeamAssociationCollection
                ->getRandomAssociation([$damienAssociation->getTeam()->getId()], Gamer::TYPE_COMPUTER)
                ->getTeam();
        } else {
            // The opponent will be Damien, for the reason explained above.
            $firstMatchTeam1 = $this->winnerTeam;
            $firstMatchTeam2 = $damienAssociation->getTeam();
        }

        return [$firstMatchTeam1, $firstMatchTeam2];
    }


    /**
     * @return \App\Model\MatchCollection
     */
    private function createCalcio() : MatchCollection
    {
        $matchCollection = new MatchCollection();
        $associations = $this->gamerTeamAssociationCollection->getAssociations();
        shuffle($associations);

        // Create the association vs association
        $matches = [];
        $matches[] = [$associations[0],$associations[1]];
        $matches[] = [$associations[0],$associations[2]];
        $matches[] = [$associations[0],$associations[3]];
        $matches[] = [$associations[1],$associations[2]];
        $matches[] = [$associations[1],$associations[3]];
        $matches[] = [$associations[2],$associations[3]];

        // Create the match objects with the team and the gamer
        $matchesObjs = [];
        foreach ($matches as $match) {
            $matchObj = new Match();
            $matchObj->setType(Match::TYPE_CHAMPIONSHIP);

            $matchObj->setTeam1($match[0]->getTeam());
            $matchObj->setTeam2($match[1]->getTeam());
            $matchObj->setGamer1($match[0]->getGamer());
            $matchObj->setGamer2($match[1]->getGamer());

            $matchesObjs[] = $matchObj;
        }

        // J1
        if ($this->isSpecial) {
            [$firstMatchTeam1, $firstMatchTeam2] = $this->getSpecialCalcioFirstMatchesTeams();
        } else {
            [$firstMatchTeam1, $firstMatchTeam2] = $this->getFirstMatchesTeams();
        }

        $exclusions = [$firstMatchTeam1->getId(), $firstMatchTeam2->getId()];
        $secondMatchTeam1 =  $this->gamerTeamAssociationCollection->getRandomAssociation($exclusions)->getTeam();
        \array_push($exclusions, $secondMatchTeam1->getId());
        $secondMatchTeam2 =  $this->gamerTeamAssociationCollection->getRandomAssociation($exclusions)->getTeam();

        // Extracting the first and the second match
        $firstMatch = null;
        $secondMatch = null;
        foreach ($matchesObjs as $key => $matchObj) {
            /** @var \App\Entity\Match $matchObj */
            if ( ($matchObj->getTeam1()->getId() === $firstMatchTeam1->getId()
                && $matchObj->getTeam2()->getId() === $firstMatchTeam2->getId())
                || ($matchObj->getTeam1()->getId() === $firstMatchTeam2->getId()
                    && $matchObj->getTeam2()->getId() === $firstMatchTeam1->getId())
                && null == $firstMatch
            ) {
                $firstMatch = $matchObj;
                unset($matchesObjs[$key]);
            }

            if ( ($matchObj->getTeam1()->getId() === $secondMatchTeam1->getId()
                    && $matchObj->getTeam2()->getId() === $secondMatchTeam2->getId())
                || ($matchObj->getTeam1()->getId() === $secondMatchTeam2->getId()
                    && $matchObj->getTeam2()->getId() === $secondMatchTeam1->getId())
                && null == $secondMatch
            ) {
                $secondMatch = $matchObj;
                unset($matchesObjs[$key]);
            }
        }

        if (null === $firstMatch || null === $secondMatch) { // Should and must never happen.
            throw new \RuntimeException('Unable to select the two first matches');
        }

        // J2
        $j2m1 = \array_shift($matchesObjs);
        $j2m1T1Id = $j2m1->getTeam1()->getId();
        $j2m1T2Id = $j2m1->getTeam2()->getId();
        $j2m2 = null;

        foreach ($matchesObjs as $key => $matchObj) {
            /** @var \App\Entity\Match $matchObj */
            $team1Id = $matchObj->getTeam1()->getId();
            $team2Id = $matchObj->getTeam2()->getId();

            if ($team1Id === $j2m1T1Id || $team1Id === $j2m1T2Id
                || $team2Id === $j2m1T1Id || $team2Id === $j2m1T2Id
            ) {
                continue;
            }
            $j2m2 = $matchObj;
            unset($matchesObjs[$key]);
            break;
        }

        if (null === $j2m2) {
            throw new \RuntimeException('Unable to select the j2m2');
        }

        // J3
        $j3m1 = \array_shift($matchesObjs);
        $j3m2 = \array_shift($matchesObjs);

        // Let's fill the collection now
        $matchCollection->add($firstMatch);
        $matchCollection->add($secondMatch);
        $matchCollection->add($j2m1);
        $matchCollection->add($j2m2);
        $matchCollection->add($j3m1);
        $matchCollection->add($j3m2);
        $matchCollection->add(clone $j2m1);
        $matchCollection->add(clone $j2m2);
        $matchCollection->add(clone $j3m1);
        $matchCollection->add(clone $j3m2);
        $matchCollection->add(clone $secondMatch);
        $matchCollection->add(clone $firstMatch);

        return $matchCollection;
    }
}
