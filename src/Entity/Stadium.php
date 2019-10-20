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

/**
 * @ORM\Entity
 * @ORM\Table(name="stadium")
 * @ApiResource
 */
class Stadium
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="stadium_id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $city;

    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Stadium
     */
    public function setId(int $id) : Stadium
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Stadium
     */
    public function setName(string $name) : Stadium
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity() : ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Stadium
     */
    public function setCity(string $city) : Stadium
    {
        $this->city = $city;

        return $this;
    }
}
