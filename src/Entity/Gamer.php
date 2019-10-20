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
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use App\Traits\TeamDataTrait;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="gamer")
 * @ApiResource
 */
class Gamer
{
    use TeamDataTrait;

    public const TYPE_HUMAN = 'human';
    public const TYPE_COMPUTER = 'computer';

    /**
     * Get the Damien's id from the .env file.
     * @return int
     */
    public static function getDamienId() : int
    {
        $id = (int) $_ENV['DAMIEN_ID'] ?? null;
        if (\filter_var($id, FILTER_VALIDATE_INT) === false
            || $id === 0
        ) {
            throw new \LogicException('Damien Id not found or properly set!');
        }

        return $id;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="gamer_id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * Defined here because of the relation.
     *
     * @var \App\Entity\Tournament[]
     * One gamer has won many titles. This is the inverse side.
     * @OneToMany(targetEntity="Tournament", mappedBy="gamerWinner")
     */
    private $titles;

    /**
     * Overridden here because of the relation.
     *
     * @var \App\Entity\Goal[]
     * One gamer has many goals. This is the inverse side.
     * @OneToMany(targetEntity="Goal", mappedBy="gamer")
     */
    private $goals;

    /**
     * Overridden here because of the relation.
     *
     * @var \App\Entity\Event[]
     * One team has many events. This is the inverse side.
     * @OneToMany(targetEntity="Event", mappedBy="gamer")
     */
    private $events;

    public function __construct()
    {
        $this->titles = new ArrayCollection();
        $this->goals = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Gamer
     */
    public function setId(int $id) : Gamer
    {
        $this->id = $id;
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
     * @return Gamer
     */
    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
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
     * @return Gamer
     */
    public function setGoals(array $goals): Gamer
    {
        $this->goals = $goals;
        return $this;
    }
}