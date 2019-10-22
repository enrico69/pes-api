<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       02/09/2019 (dd-mm-YYYY)
 */

namespace App\Traits;

use Doctrine\Common\Collections\Collection;

trait TeamDataTrait
{
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $goalsScoredCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $goalsReceivedCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $yellowCardsCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $redCardsCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $ownGoalsCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $injuriesReceivedCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $injuriesDoneCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $victoriesCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $drawsCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $defeatsCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $titleCount = 0;

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getGoalsScoredCount(): int
    {
        return $this->goalsScoredCount;
    }

    /**
     * @param int $goalsScoredCount
     *
     * @return self
     */
    public function setGoalsScoredCount(int $goalsScoredCount): self
    {
        $this->goalsScoredCount = $goalsScoredCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getGoalsReceivedCount(): int
    {
        return $this->goalsReceivedCount;
    }

    /**
     * @param int $goalsReceivedCount
     *
     * @return self
     */
    public function setGoalsReceivedCount(int $goalsReceivedCount): self
    {
        $this->goalsReceivedCount = $goalsReceivedCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getYellowCardsCount(): int
    {
        return $this->yellowCardsCount;
    }

    /**
     * @param mixed $yellowCardsCount
     *
     * @return self
     */
    public function setYellowCardsCount(int $yellowCardsCount): self
    {
        $this->yellowCardsCount = $yellowCardsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getRedCardsCount(): int
    {
        return $this->redCardsCount;
    }

    /**
     * @param int $redCardsCount
     *
     * @return self
     */
    public function setRedCardsCount(int $redCardsCount): self
    {
        $this->redCardsCount = $redCardsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getOwnGoalsCount(): int
    {
        return $this->ownGoalsCount;
    }

    /**
     * @param int $ownGoalsCount
     *
     * @return self
     */
    public function setOwnGoalsCount(int $ownGoalsCount): self
    {
        $this->ownGoalsCount = $ownGoalsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getInjuriesReceivedCount(): int
    {
        return $this->injuriesReceivedCount;
    }

    /**
     * @param int $injuriesReceivedCount
     *
     * @return self
     */
    public function setInjuriesReceivedCount(int $injuriesReceivedCount): self
    {
        $this->injuriesReceivedCount = $injuriesReceivedCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getInjuriesDoneCount(): int
    {
        return $this->injuriesDoneCount;
    }

    /**
     * @param mixed $injuriesDoneCount
     *
     * @return self
     */
    public function setInjuriesDoneCount(int $injuriesDoneCount): self
    {
        $this->injuriesDoneCount = $injuriesDoneCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getVictoriesCount(): int
    {
        return $this->victoriesCount;
    }

    /**
     * @param mixed $victoriesCount
     *
     * @return self
     */
    public function setVictoriesCount(int $victoriesCount): self
    {
        $this->victoriesCount = $victoriesCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getDrawsCount(): int
    {
        return $this->drawsCount;
    }

    /**
     * @param int $drawsCount
     *
     * @return self
     */
    public function setDrawsCount(int $drawsCount): self
    {
        $this->drawsCount = $drawsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefeatsCount(): int
    {
        return $this->defeatsCount;
    }

    /**
     * @param int $defeatsCount
     *
     * @return self
     */
    public function setDefeatsCount(int $defeatsCount): self
    {
        $this->defeatsCount = $defeatsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getTitleCount(): int
    {
        return $this->titleCount;
    }

    /**
     * @param int $titleCount
     *
     * @return self
     */
    public function setTitleCount(int $titleCount): self
    {
        $this->titleCount = $titleCount;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTitles(): Collection
    {
        return $this->titles;
    }

    /**
     * @param \App\Entity\Tournament[] $titles
     *
     * @return self
     */
    public function setTitles(array $titles): self
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGoals(): Collection
    {
        return $this->goals;
    }

    /**
     * @param \App\Entity\Goal[] $goals
     *
     * @return self
     */
    public function setGoals(array $goals): self
    {
        $this->goals = $goals;

        return $this;
    }

    /**
     * @param \App\Entity\Event[] $events
     *
     * @return self
     */
    public function setEvents(array $events): self
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function incrementVictoriesCount(): void
    {
        ++$this->victoriesCount;
    }

    public function incrementDefeatsCount(): void
    {
        ++$this->defeatsCount;
    }

    public function incrementDrawsCount(): void
    {
        ++$this->drawsCount;
    }

    public function incrementInjuryDoneCount(): void
    {
        ++$this->injuriesDoneCount;
    }

    public function incrementInjuryReceivedCount(): void
    {
        ++$this->injuriesReceivedCount;
    }

    public function incrementOwnGoalCount(): void
    {
        ++$this->ownGoalsCount;
    }

    public function incrementRedCardCount(): void
    {
        ++$this->redCardsCount;
    }

    public function incrementYellowCardCount(): void
    {
        ++$this->yellowCardsCount;
    }

    public function incrementGoalScoredCount(): void
    {
        ++$this->goalsScoredCount;
    }

    public function incrementGoalReceivedCount(): void
    {
        ++$this->goalsReceivedCount;
    }
}
