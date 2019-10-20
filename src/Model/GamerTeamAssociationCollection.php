<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       06/09/2019 (dd-mm-YYYY)
 */
namespace App\Model;

use App\Entity\Gamer;

class GamerTeamAssociationCollection implements \Iterator, \Countable
{
    /**
     * @var \App\Model\GamerTeamAssociation[]
     */
    private $associations = [];
    private $gamerIds = [];
    private $teamIds =[];
    private $locked = false;
    private $computerCount = 0;
    private $damienTeamId;

    public function add(GamerTeamAssociation $gamerTeamAssociation) : self
    {
        if ($this->isLocked()) {
            throw new \LogicException('Collection is locked. You cannot add more association!');
        }

        $gamerId = $gamerTeamAssociation->getGamer()->getId();
        $teamId = $gamerTeamAssociation->getTeam()->getId();

        if (\in_array($gamerId, $this->gamerIds)) {
            throw new \LogicException('The gamer is already present!');
        }

        if (\in_array($teamId, $this->teamIds)) {
            throw new \LogicException('The team is already present!');
        }

        $this->gamerIds[] = $gamerId;
        $this->teamIds[] = $teamId;
        $this->associations[$gamerId . $teamId] = $gamerTeamAssociation;

        if ($gamerTeamAssociation->getGamer()->getType() === Gamer::TYPE_COMPUTER) {
            $this->computerCount++;
        }

        if ($gamerId === Gamer::getDamienId()) {
            $this->damienTeamId = $teamId;
        }

        return $this;
    }

    public function getDamienTeamId() : ?int
    {
        return $this->damienTeamId;
    }

    public function getComputerCount() : int
    {
        return $this->computerCount;
    }

    public function isLocked() : bool
    {
        return $this->locked;
    }

    public function lock() : self
    {
        $this->locked = true;

        return $this;
    }

    public function getDamienAssociation() : GamerTeamAssociation
    {
        if (!$this->getDamienTeamId()) {
            throw new \LogicException('Damien is not present!');
        }

        foreach ($this->associations as $association) {
            if ($association->getGamer()->getId() === Gamer::getDamienId()) {
                return $association;
            }
        }

        throw new \LogicException('Damien is not present but was supposed to be!');
    }

    public function getAssociationByTeamId(int $teamId) : GamerTeamAssociation
    {
        foreach ($this->associations as $association) {
            if ($association->getTeam()->getId() === $teamId) {
                return $association;
            }
        }

        throw new \LogicException("No association found for the team with id '{$teamId}'");
    }

    public function getRandomAssociation(array $excludeTeams = [], ?string $excludedType = null) : GamerTeamAssociation
    {
        $associationTemp = $this->associations;

        // Applies filters
        foreach ($associationTemp as $key => $association) {
            if (\in_array($association->getTeam()->getId(), $excludeTeams)
                || (null !== $excludedType && $association->getGamer()->getType() === $excludedType)
            ) {
                unset($associationTemp[$key]);
            }
        }

        if (\count($associationTemp) === 0) {
            throw new \LogicException('There is no more player in the collection after applying the filters!');
        }

        shuffle($associationTemp);

        return $associationTemp[\array_rand($associationTemp)];
    }

    /**
     * @return \App\Model\GamerTeamAssociation[]
     */
    public function getAssociations() : array
    {
        return $this->associations;
    }

    /**
     * Return the current element
     */
    public function current() : GamerTeamAssociation
    {
        return \current($this->associations);
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        return \next($this->associations);
    }

    /**
     * Return the key of the current element
     */
    public function key() : string
    {
        return \key($this->associations);
    }

    /**
     * Checks if current position is valid
     */
    public function valid() : bool
    {
        $key = \key($this->associations);

        return ($key !== null && $key !== false);
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind() : void
    {
        \reset($this->associations);
    }

    /**
     * Count elements of an object
     */
    public function count() : int
    {
        return \count($this->getAssociations());
    }
}
