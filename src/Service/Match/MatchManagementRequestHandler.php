<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       18/09/2019 (dd-mm-YYYY)
 */

namespace App\Service\Match;

use App\Entity\Appearance;
use App\Entity\Event;
use App\Entity\Goal;
use App\Entity\Match;
use App\Model\MatchPayload;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MatchRepository;
use App\Repository\PlayerRepository;
use App\Repository\StadiumRepository;

class MatchManagementRequestHandler
{
    public const MATCH_DATA_KEY = 'matchData';
    public const MATCH_ID_KEY = 'match_id';

    public const TEAM_HOME_KEY = 'team_home_id';
    public const TEAM_AWAY_KEY = 'team_away_id';
    public const TEAM_ID_KEY = 'team_id';
    public const STADIUM_KEY = 'stadium_id';

    public const COMPO_HOME_KEY = 'compo_home';
    public const COMPO_AWAY_KEY = 'compo_away';
    public const REPLACED_BY_KEY = 'replaced_by';
    public const REPLACED_AT_KEY = 'replaced_at';
    public const PLAYER_ID_KEY = 'player_id';
    public const ASSIST_PLAYER_ID_KEY = 'assist_player_id';
    public const HAPPENED_AT_KEY = 'happened_at';

    public const GOAL_KEY = 'goals';
    public const GOAL_TYPE_KEY = 'goal_type';

    public const EVENTS_KEY = 'gameEvents';
    public const COMMENTS_KEY = 'comments';

    public const TYPE_KEY = 'type';

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $request;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var string */
    private $saveDir;
    /** @var \App\Repository\TeamRepository */
    private $teamRepository;
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;
    /** @var \App\Repository\MatchRepository */
    private $matchRepository;
    /** @var \App\Repository\PlayerRepository */
    private $playerRepository;
    /** @var \App\Repository\StadiumRepository */
    private $stadiumRepository;
    /** @var \App\Service\Match\MatchUpdateManager */
    private $matchUpdateManager;

    public function __construct(
        RequestStack $requestStack,
        LoggerInterface $logger,
        string $rootDir,
        TeamRepository $teamRepository,
        EntityManagerInterface $entityManager,
        MatchRepository $matchRepository,
        PlayerRepository $playerRepository,
        StadiumRepository $stadiumRepository,
        MatchUpdateManager $matchUpdateManager
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->logger = $logger;
        $this->saveDir = $rootDir.DIRECTORY_SEPARATOR.'save-matches'.DIRECTORY_SEPARATOR;
        $this->teamRepository = $teamRepository;
        $this->entityManager = $entityManager;
        $this->matchRepository = $matchRepository;
        $this->playerRepository = $playerRepository;
        $this->stadiumRepository = $stadiumRepository;
        $this->matchUpdateManager = $matchUpdateManager;
    }

    /**
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Exception
     */
    public function process(): void
    {
        $params = $this->request->request->all();
        $this->savePayload($params);
        $matchPayload = $this->validateAndHydrate($params);
        $this->matchUpdateManager->update($matchPayload);
    }

    /**
     * @param mixed $params mixed on purpose because we don't know what we could receive in case of error...
     *
     * @throws \Exception
     */
    private function savePayload($params): void
    {
        $jsonEncodedParams = \json_encode($params);
        $dataToSave = $this->request->headers->get('referer').PHP_EOL.$jsonEncodedParams;

        $result = file_put_contents(
            $this->saveDir.microtime(true).'.txt',
            $dataToSave
        );

        if (!$result) {
            $this->logger->warning(
                'Impossible to save the payload in to the file for the match. Payload was: '.$jsonEncodedParams
            );
        }
    }

    /**
     * @param array $params
     *
     * @return \App\Model\MatchPayload
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    private function validateAndHydrate(array $params): MatchPayload
    {
        $this->logger->info('<pre>'.print_r($params, true).'</pre>');

        if (empty($params[self::MATCH_DATA_KEY]) || !is_array($params[self::MATCH_DATA_KEY])) {
            $this->raiseError('Missing or wrong payload for the match');
        }

        // For some, strong typing will help for auto-validation

        // Respect the order !!! Changing the order can lead to unexpected behaviors. See the comment in each section.
        $params = $params[self::MATCH_DATA_KEY];
        $matchPayload = new MatchPayload();

        // Match
        $matchId = $params[self::MATCH_ID_KEY] ?? null;
        $match = $this->matchRepository->getById($matchId);
        $matchPayload->setMatch($match);

        // Stadium
        $stadiumId = $params[self::STADIUM_KEY] ?? null;
        $matchPayload->setStadium($this->stadiumRepository->getById($stadiumId));

        // Teams (and potentially reorganize gamers)
        $teams = [];
        $gamers = [];
        $this->getTeamsData($params, $teams, $gamers, $matchPayload, $match);

        /**
         * Players and Appearances (requires match, and teams)
         * Respect the order: 1) get players 2) create the elements (appearances, goals, events...) of the players.
         */
        $playerData = $this->getPlayers($params);
        $playersObjs = $this->playerRepository->getByIds(\array_keys($playerData));
        $matchPayload->setPlayers($playersObjs);
        $matchPayload->setAppearances($this->getAppearances($playerData, $playersObjs, $teams, $match));

        // Goals
        $goals = $this->getGoals($params, $playersObjs, $match, $teams, $gamers);
        $matchPayload->setGoals($goals);

        // Events
        $events = $this->getEvents($params, $playersObjs, $match, $teams, $gamers);
        $matchPayload->setEvents($events);

        // Comments
        $matchPayload->setComments($params[self::COMMENTS_KEY] ?? '');

        return $matchPayload;
    }

    private function getTeamsData(array $params, array &$teams, array &$gamers, MatchPayload $matchPayload, Match $match): void
    {
        $reorganize = false;

        $teamAwayId = (int) $params[self::TEAM_AWAY_KEY] ?? null;
        $teamAway = $this->teamRepository->getById($teamAwayId);
        if ($teamAwayId !== $match->getTeam1()->getId()
            && $teamAwayId !== $match->getTeam2()->getId()
        ) {
            $this->raiseError("Team away with id #{$teamAwayId} was not expected!");
        }
        if ($teamAway->getId() !== $match->getTeam2()->getId()) { // Because of the order generated by PES may be different.
            $reorganize = true;
        }
        $teams[$teamAwayId] = $teamAway;

        $teamHomeId = (int) $params[self::TEAM_HOME_KEY] ?? null;
        $teamHome = $this->teamRepository->getById($teamHomeId);
        if ($teamHomeId !== $match->getTeam1()->getId()
            && $teamHomeId !== $match->getTeam2()->getId()
        ) {
            $this->raiseError("Team home with id #{$teamHomeId} was not expected!");
        }
        $teams[$teamHomeId] = $teamHome;

        if ($reorganize) {
            $gamer1 = $match->getGamer1();
            $gamer2 = $match->getGamer2();
            $match->setGamer2($gamer1);
            $match->setGamer1($gamer2);

            $match->setTeam1($teamHome);
            $match->setTeam2($teamAway);
        }

        // Key is the team id, on purpose!
        $gamers[$match->getTeam1()->getId()] = $match->getGamer1();
        $gamers[$match->getTeam2()->getId()] = $match->getGamer2();
    }

    private function getGoals(array $params, array $playersObjs, Match $match, array $teams, array $gamers): array
    {
        $goals = [];

        if (false === \array_key_exists(self::GOAL_KEY, $params)) {
            return $goals;
        }

        foreach ($params[self::GOAL_KEY] as $rank => $entry) {
            if (!preg_match('/[0-9]*[-][0-9]*/', $rank)) {
                $this->raiseError('Goal: rank does not match regex!');
            }
            $goal = new Goal();
            $goal->setRank($rank);
            $goal->setMatch($match);

            $playerId = (int) $entry[self::PLAYER_ID_KEY] ?? null;
            if (!$playerId) {
                $this->raiseError('Goal: player id cannot be 0!');
            }
            if (!\array_key_exists($playerId, $playersObjs)) {
                $this->raiseError('Goal: player not found in the list.');
            }
            $goal->setPlayer($playersObjs[$playerId]);

            if (empty($entry[self::HAPPENED_AT_KEY])) {
                $this->raiseError('Missing goal minute');
            }

            $minute = explode('+', $entry[self::HAPPENED_AT_KEY]);
            $this->validateMinute($minute, 'Goal');
            $goal->setScoredAt($entry[self::HAPPENED_AT_KEY]);

            $teamId = (int) $entry[self::TEAM_ID_KEY] ?? null;
            if (!$teamId) {
                $this->raiseError('Goal: team id cannot be 0!');
            }
            if (!\array_key_exists($teamId, $teams)) {
                $this->raiseError("Goal: team #{$teamId} not found in the list.");
            }
            $goal->setTeam($teams[$teamId]);

            if (!\array_key_exists($teamId, $gamers)) {
                $this->raiseError("Goal: gamer for team id #{$teamId} not found in the list.");
            }
            $goal->setGamer($gamers[$teamId]);

            if (empty($entry[self::GOAL_TYPE_KEY])) {
                $this->raiseError('Goal: missing type key');
            }
            $goal->setType($entry[self::GOAL_TYPE_KEY]);

            $assistPlayerId = !empty($entry[self::ASSIST_PLAYER_ID_KEY]) ? (int) $entry[self::ASSIST_PLAYER_ID_KEY] : null;
            if ($assistPlayerId) {
                if (!\array_key_exists($assistPlayerId, $playersObjs)) {
                    $this->raiseError('Goal: assist player not found in the list.');
                }
                if ($assistPlayerId === $playerId) {
                    $this->raiseError('Goal: the scorer and the assist cannot be the same!');
                }
                $goal->setAssist($playersObjs[$assistPlayerId]);
            }

            $goals[] = $goal;
        }

        return $goals;
    }

    private function getPlayers(array $params): array
    {
        if (empty($params[self::COMPO_HOME_KEY]) || !is_array($params[self::COMPO_HOME_KEY])
            || empty($params[self::COMPO_AWAY_KEY]) || !is_array($params[self::COMPO_AWAY_KEY])
        ) {
            $this->raiseError('Missing players for the match');
        }

        $players = [];
        $keys = [self::COMPO_HOME_KEY, self::COMPO_AWAY_KEY];
        foreach ($keys as $key) {
            foreach ($params[$key] as $player) {
                if (empty($player[self::PLAYER_ID_KEY])) {
                    //$this->raiseError('Player id is missing');
                    continue;
                }

                $teamId = (self::COMPO_HOME_KEY === $key) ? $params[self::TEAM_HOME_KEY] : $params[self::TEAM_AWAY_KEY];
                $data = ['teamId' => $teamId];
                $subData = [];

                if (!empty($player[self::REPLACED_BY_KEY])) {
                    $players[(int) $player[self::REPLACED_BY_KEY]] = $data;
                    $subData = $this->getSubstitutionData($player);
                }

                $players[(int) $player[self::PLAYER_ID_KEY]] = \array_merge($data, $subData);
            }
        }

        return $players;
    }

    private function getSubstitutionData(array $player): array
    {
        if (empty($player[self::REPLACED_AT_KEY])) {
            $this->raiseError('Missing substitution minute');
        }
        $minute = explode('+', $player[self::REPLACED_AT_KEY]);
        $this->validateMinute($minute, 'Substitution');

        return [self::REPLACED_BY_KEY => (int) $player[self::REPLACED_BY_KEY], self::REPLACED_AT_KEY => $player[self::REPLACED_AT_KEY]];
    }

    private function validateMinute(array $minute, string $theme): void
    {
        $minuteFirstPart = (int) $minute[0];
        if (!$minuteFirstPart) {
            $this->raiseError("$theme : minute cannot be 0");
        }
        if ($minuteFirstPart > 120) {
            $this->raiseError("$theme : cannot be minute > 120!");
        }

        if (!empty($minute[1])) {
            $minuteSecondPart = (int) $minute[1];
            if (!$minuteSecondPart) {
                $this->raiseError("$theme : extra time cannot be 0!");
            }
        }
    }

    private function getAppearances(array $playerData, array $playersObjs, array $teams, Match $match): array
    {
        $appearances = [];
        foreach ($playerData as $playerId => $playerInfo) {
            $appearance = new Appearance();
            $appearance->setPlayer($playersObjs[$playerId])
                ->setTeam($teams[$playerInfo['teamId']]);

            if (!empty($playerInfo[self::REPLACED_AT_KEY])) {
                $appearance->setReplacedAt($playerInfo[self::REPLACED_AT_KEY]);
                $appearance->setReplacedBy((int) $playerInfo[self::REPLACED_BY_KEY]);
            }
            $appearance->setMatch($match);

            $appearances[$playerId] = $appearance;
        }

        return $appearances;
    }

    private function getEvents(array $params, array $playersObjs, Match $match, array $teams, array $gamers): array
    {
        $events = [];
        if (false === \array_key_exists(self::EVENTS_KEY, $params)) {
            return $events;
        }

        foreach ($params[self::EVENTS_KEY] as $element) {
            $event = new Event();
            $event->setMatch($match);

            $teamId = !empty($element[self::TEAM_ID_KEY]) ? (int) $element[self::TEAM_ID_KEY] : null;
            if (!$teamId) {
                $this->raiseError('Event: team id cannot be 0!');
            }
            if (!\array_key_exists($teamId, $teams)) {
                $this->raiseError("Event: team #{$teamId} not found in the list.");
            }
            $event->setTeam($teams[$teamId]);

            if (!\array_key_exists($teamId, $gamers)) {
                $this->raiseError("Event: gamer for team id #{$teamId} not found in the list.");
            }
            $event->setGamer($gamers[$teamId]);

            $playerId = (int) $element[self::PLAYER_ID_KEY] ?? null;
            if (!$playerId) {
                $this->raiseError('Event: player id cannot be 0!');
            }
            if (!\array_key_exists($playerId, $playersObjs)) {
                $this->raiseError('Event: player not found in the list.');
            }
            $event->setPlayer($playersObjs[$playerId]);

            if (empty($element[self::HAPPENED_AT_KEY])) {
                $this->raiseError('Missing event minute');
            }
            $minute = explode('+', $element[self::HAPPENED_AT_KEY]);
            $this->validateMinute($minute, 'Substitution');
            $event->setScoredAt($element[self::HAPPENED_AT_KEY]);

            if (empty($element[self::TYPE_KEY])) {
                $this->raiseError('Missing event type key');
            }
            if (!in_array($element[self::TYPE_KEY], Event::ALLOWED_TYPES)) {
                $this->raiseError('Event type unknown: '.$element[self::TYPE_KEY]);
            }
            $event->setType($element[self::TYPE_KEY]);

            $events[] = $event;
        }

        return  $events;
    }

    private function raiseError(string $msg): void
    {
        throw new \RuntimeException($msg.'. $_POST value was: '.\json_encode($_POST));
    }
}
