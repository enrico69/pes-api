<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       02/09/2019 (dd-mm-YYYY)
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use App\Traits\OrderTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="appearance")
 * @ApiResource
 */
class Appearance
{
    use OrderTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="appearance_id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $replacedAt;

    /**
     * @var \App\Entity\Match
     *
     * One appearance belong to one Match. This is the owning side.
     * @ManyToOne(targetEntity="Match", inversedBy="appearances")
     * @JoinColumn(name="match_id", referencedColumnName="match_id")
     */
    private $match;

    /**
     * @var \App\Entity\Player
     *
     * One appearance belong to one Player. This is the owning side.
     * @ManyToOne(targetEntity="Player", inversedBy="appearances")
     * @JoinColumn(name="player_id", referencedColumnName="player_id")
     */
    private $player;

    /**
     * @ORM\Column(type="integer", name="replaced_by", nullable=true)
     */
    private $replacedBy;

    /**
     * @var \App\Entity\Team
     *
     * One appearance belong to one team. This is the owning side.
     * @ManyToOne(targetEntity="Team")
     * @JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private $team;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Appearance
     */
    public function setId(int $id): Appearance
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getReplacedAt(): string
    {
        return $this->replacedAt;
    }

    /**
     * @param string $replacedAt
     *
     * @return Appearance
     */
    public function setReplacedAt(string $replacedAt): Appearance
    {
        $this->replacedAt = $replacedAt;

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
     * @return Appearance
     */
    public function setMatch(Match $match): Appearance
    {
        $this->match = $match;

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
     * @return Appearance
     */
    public function setPlayer(Player $player): Appearance
    {
        $this->player = $player;

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
     * @return Appearance
     */
    public function setTeam(Team $team): Appearance
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return int
     */
    public function getReplacedBy(): int
    {
        return $this->replacedBy;
    }

    /**
     * @param int $replacedBy
     *
     * @return Appearance
     */
    public function setReplacedBy(int $replacedBy)
    {
        $this->replacedBy = $replacedBy;

        return $this;
    }
}
