<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Repository;

use App\Entity\Team;
use App\Model\TeamCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class TeamRepository
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $repository;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @param \Doctrine\ORM\EntityManagerInterface $entityManager */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Team::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return \App\Entity\Team
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getById(int $id): Team
    {
        $team = $this->repository->find($id);
        if (!$team) {
            throw new EntityNotFoundException("Team with id '{$id}' not found.");
        }

        return  $team;
    }

    public function getByIds(array $teamIds): TeamCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('m');
        $qb->from(Team::class, 'm');
        $qb->where($qb->expr()->in('m.id', $teamIds));
        $result = $qb->getQuery()->getResult();

        if (count($result) !== count($teamIds)) {
            throw new \LogicException('The quantity of object returned does not match the quantity of ids!');
        }

        $collection = new TeamCollection();
        foreach ($result as $element) {
            $collection->add($element);
        }

        return $collection;
    }

    /**
     * @param array|null $excludedIds
     *
     * @return \App\Entity\Team
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getRandomTeam(?array $excludedIds = null): Team
    {
        if ($excludedIds) {
            foreach ($excludedIds as &$excludedId) {
                $excludedId = (int) $excludedId;
            }
            unset($excludedId);
        }

        $dql = 'SELECT p.id FROM '.Team::class.' p WHERE p.id NOT IN ('.implode(',', $excludedIds).')';
        $query = $this->entityManager->createQuery($dql);
        $result = $query->execute();

        if (empty($result)) {
            throw new \LogicException('No teams found matching the required criteria!');
        }

        $randId = (int) array_rand($result);

        return $this->getById($randId);
    }
}
