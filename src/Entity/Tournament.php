<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       06/09/2019 (dd-mm-YYYY)
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tournament")
 * @ApiResource
 */
class Tournament
{
    public const TYPE_CALCIO = 'calcio';
    public const TYPE_CALCIO_CUP = 'calcio_cup';

    public const TYPES = [
        self::TYPE_CALCIO,
        self::TYPE_CALCIO_CUP,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="tournament_id")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $endedAt;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var \App\Entity\Gamer
     *
     * One tournament has one winner. This is the owning side.
     * @ManyToOne(targetEntity="Gamer", inversedBy="titles")
     * @JoinColumn(name="winner_gamer_id", referencedColumnName="gamer_id")
     */
    private $gamerWinner;

    /**
     * @var \App\Entity\Team
     *
     * One tournament has one winner, but a winner may have won many tournaments. This is the owning side.
     * @ManyToOne(targetEntity="Team", inversedBy="titles")
     * @JoinColumn(name="winner_team_id", referencedColumnName="team_id")
     */
    private $teamWinner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="Team")
     * @JoinTable(name="tournament_team_top_scorer",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tournament_id", referencedColumnName="tournament_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     *   }
     * )
     */
    private $topScorerTeams = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="Player")
     * @JoinTable(name="tournament_player_top_scorer",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tournament_id", referencedColumnName="tournament_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="player_id", referencedColumnName="player_id")
     *   }
     * )
     */
    private $topScorerPlayers = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="Team")
     * @JoinTable(name="tournament_team_top_wounded_received",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tournament_id", referencedColumnName="tournament_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     *   }
     * )
     */
    private $topWoundedReceivedTeams = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="Team")
     * @JoinTable(name="tournament_team_top_wounded_done",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tournament_id", referencedColumnName="tournament_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     *   }
     * )
     */
    private $topWoundedDoneTeams = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="Team")
     * @JoinTable(name="tournament_team_top_defence",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tournament_id", referencedColumnName="tournament_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     *   }
     * )
     */
    private $topDefenceTeams = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="Team")
     * @JoinTable(name="tournament_team_top_cards",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tournament_id", referencedColumnName="tournament_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     *   }
     * )
     */
    private $topCardsTeams = [];

    /**
     * @var \App\Entity\Match[]
     * @OneToMany(targetEntity="Match", mappedBy="tournament", cascade={"persist", "remove" })
     */
    private $matches;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $extraData;

    public function __construct()
    {
        $this->matches = new ArrayCollection();
        $this->topCardsTeams = new ArrayCollection();
        $this->topDefenceTeams = new ArrayCollection();
        $this->topScorerPlayers = new ArrayCollection();
        $this->topWoundedDoneTeams = new ArrayCollection();
        $this->topWoundedReceivedTeams = new ArrayCollection();
        $this->topScorerTeams = new ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Tournament
     */
    public function setCreatedAt(\DateTime $createdAt): Tournament
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return \App\Entity\Tournament
     */
    public function setType($type): Tournament
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Tournament
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \App\Entity\Gamer
     */
    public function getGamerWinner(): ?Gamer
    {
        return $this->gamerWinner;
    }

    /**
     * @param \App\Entity\Gamer $gamerWinner
     *
     * @return Tournament
     */
    public function setGamerWinner(Gamer $gamerWinner): Tournament
    {
        $this->gamerWinner = $gamerWinner;

        return $this;
    }

    /**
     * @return \App\Entity\Team
     */
    public function getTeamWinner(): ?Team
    {
        return $this->teamWinner;
    }

    /**
     * @param \App\Entity\Team $teamWinner
     *
     * @return Tournament
     */
    public function setTeamWinner(Team $teamWinner): Tournament
    {
        $this->teamWinner = $teamWinner;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatches(): Collection
    {
        return $this->matches;
    }

    /**
     * @param \App\Entity\Match[] $matches
     *
     * @return Tournament
     */
    public function setMatches(array $matches): Tournament
    {
        $this->matches = $matches;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndedAt(): ?\DateTime
    {
        return $this->endedAt;
    }

    /**
     * @param \DateTime $endedAt
     *
     * @return Tournament
     */
    public function setEndedAt(\DateTime $endedAt): Tournament
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopScorerTeams(): Collection
    {
        return $this->topScorerTeams;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $topScorerTeams
     *
     * @return Tournament
     */
    public function setTopScorerTeams(Collection $topScorerTeams): Tournament
    {
        $this->topScorerTeams = $topScorerTeams;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopScorerPlayers(): Collection
    {
        return $this->topScorerPlayers;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $topScorerPlayers
     *
     * @return Tournament
     */
    public function setTopScorerPlayers(Collection $topScorerPlayers): Tournament
    {
        $this->topScorerPlayers = $topScorerPlayers;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopWoundedReceivedTeams(): Collection
    {
        return $this->topWoundedReceivedTeams;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $topWoundedReceivedTeams
     *
     * @return Tournament
     */
    public function setTopWoundedReceivedTeams(Collection $topWoundedReceivedTeams): Tournament
    {
        $this->topWoundedReceivedTeams = $topWoundedReceivedTeams;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopWoundedDoneTeams(): Collection
    {
        return $this->topWoundedDoneTeams;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $topWoundedDoneTeams
     *
     * @return Tournament
     */
    public function setTopWoundedDoneTeams(Collection $topWoundedDoneTeams): Tournament
    {
        $this->topWoundedDoneTeams = $topWoundedDoneTeams;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopDefenceTeams(): Collection
    {
        return $this->topDefenceTeams;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $topDefenceTeams
     *
     * @return Tournament
     */
    public function setTopDefenceTeams(Collection $topDefenceTeams): Tournament
    {
        $this->topDefenceTeams = $topDefenceTeams;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopCardsTeams(): Collection
    {
        return $this->topCardsTeams;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $topCardsTeams
     *
     * @return Tournament
     */
    public function setTopCardsTeams(Collection $topCardsTeams): Tournament
    {
        $this->topCardsTeams = $topCardsTeams;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtraData(): ?string
    {
        return $this->extraData;
    }

    /**
     * @param string $extraData
     *
     * @return Tournament
     */
    public function setExtraData(string $extraData): Tournament
    {
        $this->extraData = $extraData;

        return $this;
    }
}
