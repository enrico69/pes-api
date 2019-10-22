<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       22/10/2019 (dd-mm-YYYY)
 */
namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait OrderTrait
{
    /**
     * @ORM\Column(type="integer", name="event_order", nullable=true)
     *
     * @return self
     */
    private $order;

    public function getOrder() : ?int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return $this
     */
    public function setOrder(int $order) : self
    {
        $this->order = $order;

        return $this;
    }
}