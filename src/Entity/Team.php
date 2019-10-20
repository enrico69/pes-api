<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       02/09/2019 (dd-mm-YYYY)
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\TeamDataTrait;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity
 * @ApiResource
 */
class Team
{
    use TeamDataTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="team_id")
     */
    private $id;

    /**
     * @var \App\Entity\Player[]
     * @ManyToMany(targetEntity="Player", mappedBy="teams")
     */
    private $players;

    /**
     * Overridden here because of the relation.
     *
     * @var \App\Entity\Tournament[]
     * One team has won many titles. This is the inverse side.
     * @OneToMany(targetEntity="Tournament", mappedBy="teamWinner")
     */
    private $titles;

    /**
     * Overridden here because of the relation.
     *
     * @var \App\Entity\Goal[]
     * One team has many goals. This is the inverse side.
     * @OneToMany(targetEntity="Goal", mappedBy="team")
     */
    private $goals;

    /**
     * Overridden here because of the relation.
     *
     * @var \App\Entity\Event[]
     * One team has many events. This is the inverse side.
     * @OneToMany(targetEntity="Event", mappedBy="team")
     */
    private $events;

    /**
     * Rank from 1 (big team) to 4 (weak).
     *
     * @ORM\Column(type="integer", name="rank", options={"default" : 4})
     */
    private $rank;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->titles = new ArrayCollection();
        $this->goals = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Team
     */
    public function setId(int $id) : Team
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers() : Collection
    {
        return $this->players;
    }

    public function setPlayers(array $players) : Team
    {
        $this->players = $players;
        return  $this;
    }

    /**
     * @return Collection
     */
    public function getGoals(): Collection
    {
        return $this->goals;
    }

    /**
     * @param \App\Entity\Goal[] $goals
     * @return Team
     */
    public function setGoals(array $goals): Team
    {
        $this->goals = $goals;
        return $this;
    }

    /**
     * @return int
     */
    public function getRank() : int
    {
        return $this->rank;
    }
}