<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       13/09/2019 (dd-mm-YYYY)
 */

namespace App\Service\Tournament;

use App\Entity\Tournament;
use App\Factory\GamerTeamAssociationCollectionFactory;
use App\Factory\Tournament\FourTeamsCalcioFactory;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Factory\TournamentFactory;

class TournamentCreationManager
{
    /** @var \App\Factory\GamerTeamAssociationCollectionFactory */
    private $gamerTeamAssociationCollectionFactory;
    /** @var \App\Factory\FourTeamsCalcioFactory */
    private $fourTeamsCalcioFactory;
    /** @var \App\Repository\TeamRepository */
    private $teamRepository;
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;
    /** @var \App\Factory\TournamentFactory */
    private $tournamentFactory;

    public function __construct(
        GamerTeamAssociationCollectionFactory $gamerTeamAssociationCollectionFactory,
        FourTeamsCalcioFactory $fourTeamsCalcioFactory,
        TeamRepository $teamRepository,
        EntityManagerInterface $entityManager,
        TournamentFactory $tournamentFactory
    ) {
        $this->gamerTeamAssociationCollectionFactory = $gamerTeamAssociationCollectionFactory;
        $this->fourTeamsCalcioFactory = $fourTeamsCalcioFactory;
        $this->teamRepository = $teamRepository;
        $this->entityManager = $entityManager;
        $this->tournamentFactory = $tournamentFactory;
    }

    /**
     * @param array $associations (key = gamer id, value = team id)
     * @param string   $type
     * @param int|null $winnerId
     * @throws \Exception
     */
    public function generate(
        array $associations,
        string $type,
        ?int $winnerId = null
    ) {
        $winnerTeam = null;
        if ($winnerId) {
            $winnerTeam = $this->teamRepository->getById($associations[$winnerId]);
        }

        $associationCollection = $this->gamerTeamAssociationCollectionFactory->createFromAssociativeArray($associations);
        $associationCollection->lock();
        $tournament = $this->tournamentFactory->generate($type);

        switch ($type) {
            case Tournament::TYPE_CALCIO:
                $matches = $this->fourTeamsCalcioFactory->generate($associationCollection, $winnerTeam);
                break;
            default:
                throw new \RuntimeException("Tournament type '{$type}' is unknown!");
        }

        $tournament->setMatches($matches->getMatches());
        $this->entityManager->persist($tournament);
        $this->entityManager->flush();
        foreach ($matches->getMatches() as $match) {
            $match->setTournament($tournament);
        }
        $this->entityManager->flush();
    }
}
