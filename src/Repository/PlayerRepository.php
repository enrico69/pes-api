<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Repository;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class PlayerRepository
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $repository;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @param \Doctrine\ORM\EntityManagerInterface $entityManager */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Player::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return \App\Entity\Player
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getById(int $id): Player
    {
        $player = $this->repository->find($id);
        if (!$player) {
            throw new EntityNotFoundException("Player with id '{$id}' not found.");
        }

        return  $player;
    }

    public function getByIds(array $playerIds): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('m');
        $qb->from(Player::class, 'm');
        $qb->where($qb->expr()->in('m.id', $playerIds));
        $result = $qb->getQuery()->getResult();

        if (count($result) !== count($playerIds)) {
            throw new \LogicException('The quantity of object returned does not match the quantity of ids!');
        }

        $players = [];
        foreach ($result as $player) {
            /* @var \App\Entity\Player $player */
            $players[$player->getId()] = $player;
        }

        return $players;
    }

    public function getByTeam(int $teamId): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Player::class, 'p')
            ->join('p.teams', 't')
            ->addSelect('t')
            ->where('t.id = '.$teamId)
            ->orderBy('p.lastName');

        return $qb->getQuery()->getResult();
    }
}
