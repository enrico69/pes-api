<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       20/10/2019 (dd-mm-YYYY)
 */

namespace App\Factory\Tournament;

use App\Entity\Match;
use App\Entity\Gamer;
use App\Model\GamerTeamAssociationCollection;
use App\Model\MatchCollection;

class FourCupFactory
{
    /** @var \App\Model\GamerTeamAssociationCollection */
    private $gamerTeamAssociationCollection;
    private $isSpecial = false;

    /**
     * @param \App\Model\GamerTeamAssociationCollection $gamerTeamAssociationCollection
     *
     * @return \App\Model\MatchCollection
     */
    public function generate(GamerTeamAssociationCollection $gamerTeamAssociationCollection)
    {
        if (!$gamerTeamAssociationCollection->isLocked()) {
            throw new \LogicException('The collection is not locked!');
        }

        if (4 !== \count($gamerTeamAssociationCollection)) {
            throw new \LogicException('The collection does not contain 4 associations!');
        }

        $this->gamerTeamAssociationCollection = $gamerTeamAssociationCollection;

        // Damien is here AND one gamer is managed by the computer.
        if (1 === $gamerTeamAssociationCollection->getComputerCount()
            && $gamerTeamAssociationCollection->getDamienTeamId()) {
            $this->isSpecial = true;
        }

        return $this->createCup();
    }

    private function getFirstMatchesTeams(): array
    {
        $team1 = $this->gamerTeamAssociationCollection
            ->getRandomAssociation()
            ->getTeam();

        $team2 = $this->gamerTeamAssociationCollection
            ->getRandomAssociation([$team1->getId()])
            ->getTeam();

        $teams = [$team1, $team2];
        \shuffle($teams);

        return $teams;
    }

    private function getSpecialCupFirstMatchesTeams(): array
    {
        $damienAssociation = $this->gamerTeamAssociationCollection->getDamienAssociation();
        $computerAssociation = $this->gamerTeamAssociationCollection->getRandomAssociation([], Gamer::TYPE_HUMAN);
        $randomHumanTeam = $this->gamerTeamAssociationCollection
            ->getRandomAssociation([$damienAssociation->getTeam()->getId()], Gamer::TYPE_COMPUTER)
            ->getTeam();

        if ($computerAssociation->getTeam()->getRank() > 1) {
            $teams = [$damienAssociation->getTeam(), $computerAssociation->getTeam()];
        } else {
            $teams = [$damienAssociation->getTeam(), $randomHumanTeam];
        }
        \shuffle($teams);

        return $teams;
    }

    /**
     * @return \App\Model\MatchCollection
     */
    private function createCup(): MatchCollection
    {
        if ($this->isSpecial) {
            $match1Teams = $this->getSpecialCupFirstMatchesTeams();
        } else {
            $match1Teams = $this->getFirstMatchesTeams();
        }

        $match2Team1 = $this->gamerTeamAssociationCollection
            ->getRandomAssociation([$match1Teams[0]->getId(), $match1Teams[1]->getId()])
            ->getTeam();

        $match2Team2 = $this->gamerTeamAssociationCollection
            ->getRandomAssociation([$match1Teams[0]->getId(), $match1Teams[1]->getId(), $match2Team1->getId()])
            ->getTeam();

        $matchCollection = new MatchCollection();

        $matchObj1 = new Match();
        $matchObj1->setType(Match::TYPE_CUP_FIRST_LEG);
        $matchObj1->setTeam1($match1Teams[0]);
        $matchObj1->setGamer1($this->gamerTeamAssociationCollection->getAssociationByTeamId($match1Teams[0]->getId())->getGamer());
        $matchObj1->setTeam2($match1Teams[1]);
        $matchObj1->setGamer2($this->gamerTeamAssociationCollection->getAssociationByTeamId($match1Teams[1]->getId())->getGamer());
        $matchCollection->add($matchObj1);

        $matchObj2 = new Match();
        $matchObj2->setType(Match::TYPE_CUP_FIRST_LEG);
        $matchObj2->setTeam1($match2Team1);
        $matchObj2->setGamer1($this->gamerTeamAssociationCollection->getAssociationByTeamId($match2Team1->getId())->getGamer());
        $matchObj2->setTeam2($match2Team2);
        $matchObj2->setGamer2($this->gamerTeamAssociationCollection->getAssociationByTeamId($match2Team2->getId())->getGamer());
        $matchCollection->add($matchObj2);

        $matchObj3 = new Match();
        $matchObj3->setType(Match::TYPE_CUP_SECOND_LEG);
        $matchObj3->setTeam1($matchObj1->getTeam2());
        $matchObj3->setGamer1($matchObj1->getGamer2());
        $matchObj3->setTeam2($matchObj1->getTeam1());
        $matchObj3->setGamer2($matchObj1->getGamer1());
        $matchCollection->add($matchObj3);

        $matchObj4 = new Match();
        $matchObj4->setType(Match::TYPE_CUP_SECOND_LEG);
        $matchObj4->setTeam1($matchObj2->getTeam2());
        $matchObj4->setGamer1($matchObj2->getGamer2());
        $matchObj4->setTeam2($matchObj2->getTeam1());
        $matchObj4->setGamer2($matchObj2->getGamer1());
        $matchCollection->add($matchObj4);

        $final = clone $matchObj4;
        $final->setType(Match::TYPE_CUP_SIMPLE);

        $matchCollection->add($final);

        return $matchCollection;
    }
}
