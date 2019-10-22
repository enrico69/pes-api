<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Repository;

use App\Entity\Stadium;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class StadiumRepository
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $repository;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @param \Doctrine\ORM\EntityManagerInterface $entityManager */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Stadium::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return \App\Entity\Stadium
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getById(int $id): Stadium
    {
        $stadium = $this->repository->find($id);
        if (!$stadium) {
            throw new EntityNotFoundException("Stadium with id '{$id}' not found.");
        }

        return  $stadium;
    }
}
