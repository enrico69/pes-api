<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Factory;


use App\Repository\TeamRepository;
use App\Model\TeamCollection;

class TeamCollectionFactory
{
    /** @var \App\Repository\TeamRepository */
    private $teamRepository;

    /**
     * @param \App\Repository\TeamRepository $teamRepository
     */
    public function __construct(TeamRepository $teamRepository) {
        $this->teamRepository = $teamRepository;
    }

    /**
     * @param array $teamIds
     *
     * @return \App\Model\TeamCollection
     */
    public function createFromArray(array $teamIds) : TeamCollection
    {
        foreach ($teamIds as $teamId) {
            if (false === filter_var($teamId, FILTER_VALIDATE_INT)
                || $teamId === 0
            ) {
                throw new \RuntimeException("Gamer id '{$teamId}' is not an valid int!");
            }
        }

        return $this->teamRepository->getByIds($teamIds);
    }
}