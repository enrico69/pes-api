<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       02/09/2019 (dd-mm-YYYY)
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="game")
 * @ApiResource
 */
class Match
{
    public const TYPE_CHAMPIONSHIP = 'championship';
    public const TYPE_CUP_SIMPLE = 'cup_simple';
    public const TYPE_CUP_FIRST_LEG = 'cup_first_leg';
    public const TYPE_CUP_SECOND_LEG = 'cup_second_leg';

    public const TYPES = [
        self::TYPE_CHAMPIONSHIP => 'Championnat',
        self::TYPE_CUP_FIRST_LEG => 'Coupe match aller',
        self::TYPE_CUP_SECOND_LEG => 'Coupe match retour',
        self::TYPE_CUP_SIMPLE => 'Coupe',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="match_id")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var \App\Entity\Gamer
     *
     * This is the owning side
     * @ManyToOne(targetEntity="Gamer")
     * @JoinColumn(name="gamer_1", referencedColumnName="gamer_id")
     */
    private $gamer1;

    /**
     * @var \App\Entity\Gamer
     *
     * This is the owning side
     * @ManyToOne(targetEntity="Gamer")
     * @JoinColumn(name="gamer_2", referencedColumnName="gamer_id")
     */
    private $gamer2;

    /**
     * @var \App\Entity\Team
     *
     * This is the owning side
     * @ManyToOne(targetEntity="Team")
     * @JoinColumn(name="team_1", referencedColumnName="team_id")
     */
    private $team1;

    /**
     * @var \App\Entity\Team
     *
     * This is the owning side
     * @ManyToOne(targetEntity="Team")
     * @JoinColumn(name="team_2", referencedColumnName="team_id")
     */
    private $team2;

    /**
     * @var \App\Entity\Gamer
     *
     * This is the owning side
     * @ManyToOne(targetEntity="Gamer")
     * @JoinColumn(name="winner_gamer", referencedColumnName="gamer_id")
     */
    private $winnerGamer;

    /**
     * @var \App\Entity\Team
     *
     * This is the owning side
     * @ManyToOne(targetEntity="Team")
     * @JoinColumn(name="winner_team", referencedColumnName="team_id")
     */
    private $winnerTeam;

    /**
     * @var \App\Entity\Tournament
     *
     * One match belongs to one tournament. This is the owning side.
     * @ManyToOne(targetEntity="Tournament", inversedBy="matches")
     * @JoinColumn(name="tournament_id", referencedColumnName="tournament_id")
     */
    private $tournament;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team1HalfTimeScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team2HalfTimeScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team1FullTimeScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team2FullTimeScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team1ProlongHalfTimeScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team2ProlongHalfTimeScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team1ProlongFullTimeScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team2ProlongFullTimeScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team1PenaltyScore = 0;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default" : 0})
     */
    private $team2PenaltyScore = 0;

    /**
     * @var \App\Entity\Stadium
     *
     * This is the owning side
     * @ManyToOne(targetEntity="Stadium")
     * @JoinColumn(name="stadium", referencedColumnName="stadium_id")
     */
    private $stadium;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var \App\Entity\Goal[]
     *                         One match has many goals. This is the inverse side.
     * @OneToMany(targetEntity="Goal", mappedBy="match", cascade={"persist", "remove" })
     */
    private $goals;

    /**
     * Overridden here because of the relation.
     *
     * @var \App\Entity\Event[]
     *                          One match has many events. This is the inverse side.
     * @OneToMany(targetEntity="Event", mappedBy="match", cascade={"persist", "remove" })
     */
    private $events;

    /**
     * @var \App\Entity\Appearance[]
     *                               One match has many appearances. This is the inverse side.
     * @OneToMany(targetEntity="Appearance", mappedBy="match", cascade={"persist", "remove" })
     */
    private $appearances;

    public function __construct()
    {
        $this->goals = new ArrayCollection();
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
     * @param int $id
     *
     * @return Match
     */
    public function setId(int $id): Match
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return Match
     */
    public function setDate(\DateTime $date): Match
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Match
     */
    public function setType(string $type): Match
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam1HalfTimeScore(): int
    {
        return $this->team1HalfTimeScore;
    }

    /**
     * @param int $team1HalfTimeScore
     *
     * @return Match
     */
    public function setTeam1HalfTimeScore(int $team1HalfTimeScore): Match
    {
        $this->team1HalfTimeScore = $team1HalfTimeScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam2HalfTimeScore(): int
    {
        return $this->team2HalfTimeScore;
    }

    /**
     * @param int $team2HalfTimeScore
     *
     * @return Match
     */
    public function setTeam2HalfTimeScore(int $team2HalfTimeScore): Match
    {
        $this->team2HalfTimeScore = $team2HalfTimeScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam1FullTimeScore(): int
    {
        return $this->team1FullTimeScore;
    }

    /**
     * @param int $team1FullTimeScore
     *
     * @return Match
     */
    public function setTeam1FullTimeScore(int $team1FullTimeScore): Match
    {
        $this->team1FullTimeScore = $team1FullTimeScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam2FullTimeScore(): int
    {
        return $this->team2FullTimeScore;
    }

    /**
     * @param int $team2FullTimeScore
     *
     * @return Match
     */
    public function setTeam2FullTimeScore(int $team2FullTimeScore): Match
    {
        $this->team2FullTimeScore = $team2FullTimeScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam1ProlongHalfTimeScore(): int
    {
        return $this->team1ProlongHalfTimeScore;
    }

    /**
     * @param int $team1ProlongHalfTimeScore
     *
     * @return Match
     */
    public function setTeam1ProlongHalfTimeScore(int $team1ProlongHalfTimeScore): Match
    {
        $this->team1ProlongHalfTimeScore = $team1ProlongHalfTimeScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam2ProlongHalfTimeScore(): int
    {
        return $this->team2ProlongHalfTimeScore;
    }

    /**
     * @param int $team2ProlongHalfTimeScore
     *
     * @return Match
     */
    public function setTeam2ProlongHalfTimeScore(int $team2ProlongHalfTimeScore): Match
    {
        $this->team2ProlongHalfTimeScore = $team2ProlongHalfTimeScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam1ProlongFullTimeScore(): int
    {
        return $this->team1ProlongFullTimeScore;
    }

    /**
     * @param int $team1ProlongFullTimeScore
     *
     * @return Match
     */
    public function setTeam1ProlongFullTimeScore(int $team1ProlongFullTimeScore): Match
    {
        $this->team1ProlongFullTimeScore = $team1ProlongFullTimeScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam2ProlongFullTimeScore(): int
    {
        return $this->team2ProlongFullTimeScore;
    }

    /**
     * @param int $team2ProlongFullTimeScore
     *
     * @return Match
     */
    public function setTeam2ProlongFullTimeScore(int $team2ProlongFullTimeScore): Match
    {
        $this->team2ProlongFullTimeScore = $team2ProlongFullTimeScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam1PenaltyScore(): int
    {
        return $this->team1PenaltyScore;
    }

    /**
     * @param int $team1PenaltyScore
     *
     * @return Match
     */
    public function setTeam1PenaltyScore(int $team1PenaltyScore): Match
    {
        $this->team1PenaltyScore = $team1PenaltyScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam2PenaltyScore(): int
    {
        return $this->team2PenaltyScore;
    }

    /**
     * @param int $team2PenaltyScore
     *
     * @return Match
     */
    public function setTeam2PenaltyScore(int $team2PenaltyScore): Match
    {
        $this->team2PenaltyScore = $team2PenaltyScore;

        return $this;
    }

    /**
     * @return \App\Entity\Tournament
     */
    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    /**
     * @param \App\Entity\Tournament $tournament
     *
     * @return Match
     */
    public function setTournament(Tournament $tournament): Match
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * @return \App\Entity\Gamer
     */
    public function getGamer1(): Gamer
    {
        return $this->gamer1;
    }

    /**
     * @param \App\Entity\Gamer $gamer1
     *
     * @return Match
     */
    public function setGamer1(Gamer $gamer1): Match
    {
        $this->gamer1 = $gamer1;

        return $this;
    }

    /**
     * @return \App\Entity\Gamer
     */
    public function getGamer2(): Gamer
    {
        return $this->gamer2;
    }

    /**
     * @param \App\Entity\Gamer $gamer2
     *
     * @return Match
     */
    public function setGamer2(Gamer $gamer2): Match
    {
        $this->gamer2 = $gamer2;

        return $this;
    }

    /**
     * @return \App\Entity\Team
     */
    public function getTeam1(): Team
    {
        return $this->team1;
    }

    /**
     * @param \App\Entity\Team $team1
     *
     * @return Match
     */
    public function setTeam1(Team $team1): Match
    {
        $this->team1 = $team1;

        return $this;
    }

    /**
     * @return \App\Entity\Team
     */
    public function getTeam2(): Team
    {
        return $this->team2;
    }

    /**
     * @param \App\Entity\Team $team2
     *
     * @return Match
     */
    public function setTeam2(Team $team2): Match
    {
        $this->team2 = $team2;

        return $this;
    }

    /**
     * @return \App\Entity\Gamer
     */
    public function getWinnerGamer(): Gamer
    {
        return $this->winnerGamer;
    }

    /**
     * @param \App\Entity\Gamer $winnerGamer
     *
     * @return Match
     */
    public function setWinnerGamer(Gamer $winnerGamer): Match
    {
        $this->winnerGamer = $winnerGamer;

        return $this;
    }

    /**
     * @return \App\Entity\Team
     */
    public function getWinnerTeam(): Team
    {
        return $this->winnerTeam;
    }

    /**
     * @param \App\Entity\Team $winnerTeam
     *
     * @return Match
     */
    public function setWinnerTeam(Team $winnerTeam): Match
    {
        $this->winnerTeam = $winnerTeam;

        return $this;
    }

    /**
     * @return \App\Entity\Stadium
     */
    public function getStadium(): Stadium
    {
        return $this->stadium;
    }

    /**
     * @param \App\Entity\Stadium $stadium
     *
     * @return Match
     */
    public function setStadium(Stadium $stadium): Match
    {
        $this->stadium = $stadium;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return Match
     */
    public function setComment(string $comment): Match
    {
        $this->comment = $comment;

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
     *
     * @return Match
     */
    public function setGoals(array $goals): Match
    {
        $this->goals = $goals;

        return $this;
    }

    /**
     * @param \App\Entity\Event[] $events
     *
     * @return Match
     */
    public function setEvents(array $events): Match
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

    /**
     * @param \App\Entity\Appearance[] $appearances
     *
     * @return Match
     */
    public function setAppearances(array $appearances): Match
    {
        $this->appearances = $appearances;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAppearances(): Collection
    {
        return $this->events;
    }
}
