<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       29/08/2019 (dd-mm-YYYY)
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="player",
 *     indexes={
 *          @ORM\Index(name="player_last_name", columns={"last_name"}),
 *      }
 * )
 * @ApiResource
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="player_id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $firstName;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $goalsCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $assistsCount = 0;

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
    private $injuriesCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $matchesCount = 0;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="Team", inversedBy="players")
     * @JoinTable(name="player_team",
     *   joinColumns={
     *     @ORM\JoinColumn(name="player_id", referencedColumnName="player_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     *   }
     * )
     */
    private $teams = [];

    /**
     * @var \App\Entity\Goal[]
     * One player has many goals. This is the inverse side.
     * @OneToMany(targetEntity="Goal", mappedBy="player")
     */
    private $goals;

    /**
     * @var \App\Entity\Goal[]
     * One player has many goals. This is the inverse side.
     * @OneToMany(targetEntity="Goal", mappedBy="assist")
     */
    private $assists;

    /**
     * Overridden here because of the relation.
     *
     * @var \App\Entity\Event[]
     * One player has many events. This is the inverse side.
     * @OneToMany(targetEntity="Event", mappedBy="player")
     */
    private $events;

    /**
     * @var \App\Entity\Appearance[]
     * One player has many appearances. This is the inverse side.
     * @OneToMany(targetEntity="Appearance", mappedBy="player")
     */
    private $appearances;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->goals = new ArrayCollection();
        $this->assists = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->appearances = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Player
     */
    public function setId(int $id) : Player
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return Player
     */
    public function setFirstName($firstName) : Player
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return Player
     */
    public function setLastName($lastName) : Player
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoalsCount(): int
    {
        return $this->goalsCount;
    }

    /**
     * @param int $goalsCount
     * @return Player
     */
    public function setGoalsCount(int $goalsCount) : Player
    {
        $this->goalsCount = $goalsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getAssistsCount(): int
    {
        return $this->assistsCount;
    }

    /**
     * @param int $assistsCount
     * @return Player
     */
    public function setAssistsCount(int $assistsCount) : Player
    {
        $this->assistsCount = $assistsCount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYellowCardsCount(): int
    {
        return $this->yellowCardsCount;
    }

    /**
     * @param mixed $yellowCardsCount
     * @return Player
     */
    public function setYellowCardsCount($yellowCardsCount) : Player
    {
        $this->yellowCardsCount = $yellowCardsCount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRedCardsCount(): int
    {
        return $this->redCardsCount;
    }

    /**
     * @param mixed $redCardsCount
     * @return Player
     */
    public function setRedCardsCount($redCardsCount) : Player
    {
        $this->redCardsCount = $redCardsCount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwnGoalsCount(): int
    {
        return $this->ownGoalsCount;
    }

    /**
     * @param mixed $ownGoalsCount
     * @return Player
     */
    public function setOwnGoalsCount($ownGoalsCount) : Player
    {
        $this->ownGoalsCount = $ownGoalsCount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInjuriesCount(): int
    {
        return $this->injuriesCount;
    }

    /**
     * @param mixed $injuriesCount
     * @return Player
     */
    public function setInjuriesCount($injuriesCount) : Player
    {
        $this->injuriesCount = $injuriesCount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMatchesCount(): int
    {
        return $this->matchesCount;
    }

    /**
     * @param mixed $matchesCount
     * @return Player
     */
    public function setMatchesCount($matchesCount) : Player
    {
        $this->matchesCount = $matchesCount;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreationDate(): \DateTimeInterface
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTimeInterface $creationDate
     * @return Player
     */
    public function setCreationDate(\DateTimeInterface $creationDate): Player
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate(): \DateTime
    {
        return $this->updateDate;
    }

    /**
     * @param \DateTime $updateDate
     * @return Player
     */
    public function setUpdateDate(\DateTime $updateDate): Player
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /**
     * @param \App\Entity\Team $team
     *
     * @return \App\Entity\Player
     */
    public function addTeam(Team $team) : Player
    {
        $this->teams->add($team);

        return $this;
    }

    /**
     * @param \App\Entity\Team $team
     * @return \App\Entity\Player
     */
    public function removeTeam(Team $team) : Player
    {
        $this->teams->removeElement($team);

        return $this;
    }

    /**
     * @return \App\Entity\Goal[]
     */
    public function getGoals(): array
    {
        return $this->goals;
    }

    /**
     * @param \App\Entity\Goal[] $goals
     * @return Player
     */
    public function setGoals(array $goals): Player
    {
        $this->goals = $goals;
        return $this;
    }

    /**
     * @return \App\Entity\Goal[]
     */
    public function getAssists(): array
    {
        return $this->assists;
    }

    /**
     * @param \App\Entity\Goal[] $assists
     * @return Player
     */
    public function setAssists(array $assists): Player
    {
        $this->assists = $assists;
        return $this;
    }

    /**
     * @param \App\Entity\Event[] $events
     * @return \App\Entity\Player
     */
    public function setEvents(array $events) : Player
    {
        $this->events = $events;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents() : Collection
    {
        return $this->events;
    }

    /**
     * @param \App\Entity\Appearance[] $appearances
     * @return \App\Entity\Player
     */
    public function setAppearances(array $appearances) : Player
    {
        $this->appearances = $appearances;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAppearance() : Collection
    {
        return $this->appearances;
    }

    public function incrementGoalCount() : void
    {
        $this->goalsCount++;
    }

    public function incrementAssistCount() : void
    {
        $this->assistsCount++;
    }

    public function incrementOwnGoalCount() : void
    {
        $this->ownGoalsCount++;
    }

    public function incrementInjuryCount() : void
    {
        $this->injuriesCount++;
    }

    public function incrementRedCardCount() : void
    {
        $this->redCardsCount++;
    }

    public function incrementYellowCardCount() : void
    {
        $this->yellowCardsCount++;
    }

    public function incrementMatchCount() : void
    {
        $this->matchesCount++;
    }
}
