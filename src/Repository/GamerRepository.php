<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Repository;

use App\Entity\Gamer;
use App\Model\GamerCollection;
use Doctrine\ORM\EntityManagerInterface;

class GamerRepository
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $repository;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @param \Doctrine\ORM\EntityManagerInterface $entityManager */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Gamer::class);
        $this->entityManager = $entityManager;
    }

    public function getByIds(array $gamerIds): GamerCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('m');
        $qb->from(Gamer::class, 'm');
        $qb->where($qb->expr()->in('m.id', $gamerIds));
        $result = $qb->getQuery()->getResult();

        if (count($result) !== count($gamerIds)) {
            throw new \LogicException('The quantity of object returned does not match the quantity of ids!');
        }

        $collection = new GamerCollection();
        foreach ($result as $element) {
            $collection->add($element);
        }

        return $collection;
    }
}
