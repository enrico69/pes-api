<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       02/09/2019 (dd-mm-YYYY)
 */

namespace App\Entity;

use App\Entity\Player;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use App\Entity\Team;
use Doctrine\ORM\Mapping\ManyToOne;
use App\Entity\Match;
use App\Entity\Gamer;

/**
 * @ORM\Entity
 * @ORM\Table(name="goal")
 * @ApiResource
 */
class Goal
{
    public const TYPE_INGAME_GOAL = 'ingame_goal';
    public const TYPE_PENALTY_GOAL = 'penalty_goal';
    public const TYPE_OWN_GOAL = 'own_goal';
    public const TYPE_DIRECT_FREEKICK_GOAL = 'direct_freekick_goal';
    public const TYPE_INDIRECT_FREEKICK_GOAL = 'indirect_freekick_goal';
    public const TYPE_CORNER_GOAL = 'corner_goal';

    public const GOAL_TYPES = [
      self::TYPE_INGAME_GOAL,
      self::TYPE_PENALTY_GOAL,
      self::TYPE_OWN_GOAL,
      self::TYPE_DIRECT_FREEKICK_GOAL,
      self::TYPE_INDIRECT_FREEKICK_GOAL,
      self::TYPE_CORNER_GOAL
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="goal_id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false, options={"default" : 0})
     */
    private $scoredAt;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var \App\Entity\Team
     *
     * One goal belong to one team. This is the owning side.
     * @ManyToOne(targetEntity="Team", inversedBy="goals")
     * @JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private $team;

    /**
     * @var \App\Entity\Gamer
     *
     * One goal belong to one gamer. This is the owning side.
     * @ManyToOne(targetEntity="Gamer", inversedBy="goals")
     * @JoinColumn(name="gamer_id", referencedColumnName="gamer_id")
     */
    private $gamer;

    /**
     * @var \App\Entity\Player
     *
     * One goal belong to one Player. This is the owning side.
     * @ManyToOne(targetEntity="Player", inversedBy="goals")
     * @JoinColumn(name="player_id", referencedColumnName="player_id")
     */
    private $player;

    /**
     * @var \App\Entity\Player
     *
     * One goal may be an assist from one Player. This is the owning side.
     * @ManyToOne(targetEntity="Player", inversedBy="assists")
     * @JoinColumn(name="assist_player_id", referencedColumnName="player_id")
     */
    private $assist;

    /**
     * @var \App\Entity\Match
     *
     * One goal belong to one Match. This is the owning side.
     * @ManyToOne(targetEntity="Match", inversedBy="goals")
     * @JoinColumn(name="match_id", referencedColumnName="match_id")
     */
    private $match;

    /**
     * Example: 1-0, 2-1, 0-2 ...
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $rank;

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Goal
     */
    public function setId(int $id) : Goal
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getScoredAt() : string
    {
        return $this->scoredAt;
    }

    /**
     * @param string $scoredAt
     * @return Goal
     */
    public function setScoredAt(string $scoredAt) : Goal
    {
        $this->scoredAt = $scoredAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Goal
     */
    public function setType(string $type) : Goal
    {
        if (!\in_array($type, self::GOAL_TYPES)) {
            throw new \LogicException("Invalid goal type: $type");
        }
        $this->type = $type;
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
     * @return Goal
     */
    public function setTeam(Team $team): Goal
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
     * @return Goal
     */
    public function setGamer(Gamer $gamer): Goal
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
     * @return Goal
     */
    public function setPlayer(Player $player): Goal
    {
        $this->player = $player;
        return $this;
    }

    /**
     * @return \App\Entity\Player
     */
    public function getAssist(): ?Player
    {
        return $this->assist;
    }

    /**
     * @param \App\Entity\Player $player
     * @return Goal
     */
    public function setAssist(Player $player): Goal
    {
        $this->assist = $player;
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
     * @return Goal
     */
    public function setMatch(Match $match): Goal
    {
        $this->match = $match;
        return $this;
    }

    /**
     * @return string
     */
    public function getRank() : string
    {
        return $this->rank;
    }

    /**
     * @param string $rank
     * @return Goal
     */
    public function setRank(string $rank) : Goal
    {
        $this->rank = $rank;
        return $this;
    }
}