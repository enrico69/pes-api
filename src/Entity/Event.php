<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       21/09/2019 (dd-mm-YYYY)
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use App\Traits\OrderTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @ApiResource
 */
class Event
{
    use OrderTrait;

    public const TYPE_MISSED_PENALTY = 'missed_penalty';
    public const TYPE_OFFSIDE_GOAL = 'offside_goal';
    public const TYPE_WOUND = 'wounded';
    public const TYPE_POST_HIT = 'post_hit';
    public const TYPE_YELLOW_CARD = 'yellow_card';
    public const TYPE_RED_CARD = 'red_card';
    public const PEN_SHOOTOUTS_SCORED_KEY = 'penalty_shootout_scored';
    public const PEN_SHOOTOUTS_MISSED_KEY = 'penalty_shootout_missed';
    public const TYPE_PLAYER_OUT_WITHOUT_SUB = 'player_out_without_sub';

    public const ALLOWED_TYPES = [
        self::TYPE_WOUND,
        self::TYPE_YELLOW_CARD,
        self::TYPE_RED_CARD,
        self::TYPE_MISSED_PENALTY,
        self::TYPE_OFFSIDE_GOAL,
        self::TYPE_POST_HIT,
        self::PEN_SHOOTOUTS_SCORED_KEY,
        self::PEN_SHOOTOUTS_MISSED_KEY,
        self::TYPE_PLAYER_OUT_WITHOUT_SUB,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="event_id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false, options={"default" : 0})
     */
    private $happenedAt;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var \App\Entity\Team
     *
     * One event belong to one team. This is the owning side.
     * @ManyToOne(targetEntity="Team", inversedBy="events")
     * @JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private $team;

    /**
     * @var \App\Entity\Gamer
     *
     * One event belong to one gamer. This is the owning side.
     * @ManyToOne(targetEntity="Gamer", inversedBy="events")
     * @JoinColumn(name="gamer_id", referencedColumnName="gamer_id")
     */
    private $gamer;

    /**
     * @var \App\Entity\Player
     *
     * One event belong to one Player. This is the owning side.
     * @ManyToOne(targetEntity="Player", inversedBy="events")
     * @JoinColumn(name="player_id", referencedColumnName="player_id")
     */
    private $player;

    /**
     * @var \App\Entity\Match
     *
     * One event belong to one Match. This is the owning side.
     * @ManyToOne(targetEntity="Match", inversedBy="events")
     * @JoinColumn(name="match_id", referencedColumnName="match_id")
     */
    private $match;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $comment;

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
     * @return Event
     */
    public function setId(int $id): Event
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getHappenedAt(): string
    {
        return $this->happenedAt;
    }

    /**
     * @param string $happenedAt
     *
     * @return Event
     */
    public function setScoredAt(string $happenedAt): Event
    {
        $this->happenedAt = $happenedAt;

        return $this;
    }

    /**
     * @return \App\Entity\Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @param \App\Entity\Team $team
     *
     * @return Event
     */
    public function setTeam(Team $team): Event
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return \App\Entity\Gamer
     */
    public function getGamer(): Gamer
    {
        return $this->gamer;
    }

    /**
     * @param \App\Entity\Gamer $gamer
     *
     * @return Event
     */
    public function setGamer(Gamer $gamer): Event
    {
        $this->gamer = $gamer;

        return $this;
    }

    /**
     * @return \App\Entity\Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param \App\Entity\Player $player
     *
     * @return Event
     */
    public function setPlayer(Player $player): Event
    {
        $this->player = $player;

        return $this;
    }

    /**
     * @return \App\Entity\Match
     */
    public function getMatch(): Match
    {
        return $this->match;
    }

    /**
     * @param \App\Entity\Match $match
     *
     * @return Event
     */
    public function setMatch(Match $match): Event
    {
        $this->match = $match;

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
     * @return Event
     */
    public function setType(string $type): Event
    {
        if (!\in_array($type, self::ALLOWED_TYPES)) {
            throw new \LogicException("Unrecognized event type: '{$type}'");
        }

        $this->type = $type;

        return $this;
    }

    public function setComment(string $comment): Event
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
