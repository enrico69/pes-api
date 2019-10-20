<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Factory;


use App\Repository\GamerRepository;
use App\Model\GamerCollection;

class GamerCollectionFactory
{
    /** @var \App\Repository\GamerRepository */
    private $gamerRepository;

    /**
     * @param \App\Repository\GamerRepository $gamerRepository
     */
    public function __construct(GamerRepository $gamerRepository) {
        $this->gamerRepository = $gamerRepository;
    }

    /**
     * @param array $gamerIds
     *
     * @return \App\Model\GamerCollection
     */
    public function createFromArray(array $gamerIds) : GamerCollection
    {
        foreach ($gamerIds as $gamerId) {
            if (false === filter_var($gamerId, FILTER_VALIDATE_INT)
                || $gamerId === 0
            ) {
                throw new \RuntimeException("Gamer id '{$gamerId}' is not an valid int!");
            }
        }

        return $this->gamerRepository->getByIds($gamerIds);
    }
}