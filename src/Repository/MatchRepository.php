<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Repository;

use App\Entity\Match;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NoResultException;
use Doctrine\DBAL\FetchMode;

class MatchRepository
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $repository;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @param \Doctrine\ORM\EntityManagerInterface $entityManager */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository     = $entityManager->getRepository(Match::class);
        $this->entityManager  = $entityManager;
    }

    /**
     * @param int $id
     * @return \App\Entity\Match
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getById(int $id) : Match
    {
        $match = $this->repository->find($id);
        if (!$match) {
            throw new EntityNotFoundException("Match with id '{$id}' not found.");
        }

        return  $match;
    }

    /**
     * Return the list of the matches not played yet.
     * @return array
     */
    public function findNotDone() : array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('m');
        $qb->from(Match::class, 'm');
        $qb->where('m.date is NULL');

        return $qb->getQuery()->getResult();
    }
}
