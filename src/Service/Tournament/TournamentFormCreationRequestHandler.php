<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       16/09/2019 (dd-mm-YYYY)
 */

namespace App\Service\Tournament;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\TeamRepository;

class TournamentFormCreationRequestHandler
{
    public const TYPE_FIELD = 'type';
    public const WINNER_ID_FIELD = 'winnerGamerId';
    public const ASSOCIATIONS_ID_FIELD = 'associations';
    public const FORCED_TEAM_ID_FIELD = 'forcedTeamId';

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;
    /** @var \App\Service\TournamentCreationManager */
    private $tournamentCreationManager;
    /** @var bool */
    private $autoSelect;
    /** @var \App\Repository\TeamRepository */
    private $teamRepository;


    public function __construct(
        RequestStack $request,
        TournamentCreationManager $tournamentCreationManager,
        TeamRepository $teamRepository
    ) {
        /**
         * Note that injecting the payload could be more pertinent instead of the request, if we want to creat
         * the tournament from elsewhere.
         */
        $this->request = $request->getCurrentRequest();
        $this->tournamentCreationManager = $tournamentCreationManager;
        $this->teamRepository = $teamRepository;
    }

    /**
     * @throws \Exception
     */
    public function process() : void
    {
        $params = $this->request->request->all();

        // @TODO change a little the structure when adding the possibility to add or not to add a cup linked to the tournament
        $this->validate($params);

        if ($this->autoSelect) {
            $this->addRandomTeam($params);
        }

        $this->tournamentCreationManager->generate(
            $params[self::ASSOCIATIONS_ID_FIELD],
            $params[self::TYPE_FIELD],
            $params[self::WINNER_ID_FIELD] ?? null
        );
    }

    private function validate(array $data) : void
    {
        //  array (size=4)
        //  'type' => string 'calcio' (length=6)
        //  'withCup' => string 'true' (length=4)
        //  'winnerGamerId' => string '2' (length=1)
        //  'associations' =>
        //    array (size=4)
        //      1 => string '1' (length=1)
        //      2 => string '3' (length=1)
        //      3 => string '11' (length=2)
        //      4 => string '0' (length=1)


        // Calcio Type
        if (empty($data[self::TYPE_FIELD])) {
            throw new \LogicException('The type of the tournament is missing!');
        }

        // Winner Id (optional)
        if (!empty($data[self::WINNER_ID_FIELD])
            && (false === filter_var($data[self::WINNER_ID_FIELD], FILTER_VALIDATE_INT)
                || 0 === (int) $data[self::WINNER_ID_FIELD]
            )
        ) {
            throw new \LogicException('If given, Winner Id must be an int > 0!');
        }

        // Associations
        if (empty($data[self::ASSOCIATIONS_ID_FIELD])) {
            throw new \LogicException('The association data are missing!');
        }

        if (false === is_array($data[self::ASSOCIATIONS_ID_FIELD])
            || count($data[self::ASSOCIATIONS_ID_FIELD]) !== 4
        ) {
            throw new \LogicException('Invalid associations data: ' . \serialize($data[self::ASSOCIATIONS_ID_FIELD]));
        }

        $this->autoSelect = false;
        foreach ($data[self::ASSOCIATIONS_ID_FIELD] as $gamerId => $teamId) {
            if (false === filter_var($gamerId, FILTER_VALIDATE_INT)
                || 0 === $gamerId
            ) {
                throw new \LogicException('Gamer id must be an int > 0!');
            }

            if (false === filter_var($teamId, FILTER_VALIDATE_INT)) {
                throw new \LogicException('Team id must be an int!');
            }

            if ((int) $teamId === 0) {
                if ($this->autoSelect) {
                    throw new \LogicException('Only one team can be in autoselect!');
                }
                $this->autoSelect = true;
            }
        }
    }

    /**
     * @param array $params
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    private function addRandomTeam(array &$params) : void
    {
        // You can force a team if you want (ex: for debugging)
        $forcedTeamId = (int) $params[self::FORCED_TEAM_ID_FIELD];
        if ($forcedTeamId !== 0 && false === \in_array($forcedTeamId, $params[self::ASSOCIATIONS_ID_FIELD])) {
            // We load it to check it exists.
            $randomTeam = $this->teamRepository->getById($forcedTeamId);
        } else {
            // Otherwise: random
            $randomTeam = $this->teamRepository->getRandomTeam($params[self::ASSOCIATIONS_ID_FIELD]);
        }

        foreach ($params[self::ASSOCIATIONS_ID_FIELD] as &$teamId) {
            if ((int) $teamId === 0) {
                $teamId = $randomTeam->getId();

                return;
            }
        }
        unset($teamId);

        throw new \LogicException('No team found with id 0!');
    }
}
