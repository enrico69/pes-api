<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       14/09/2019 (dd-mm-YYYY)
 */

namespace App\Controller\Match;

use App\Entity\Match;
use App\Entity\Player;
use App\Entity\Stadium;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;
use App\Service\Match\MatchManagementRequestHandler;
use App\Repository\MatchRepository;

/**
 * @Route("/match-management")
 */
class Management extends  AbstractController
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \App\Service\Match\MatchManagementRequestHandler */
    private $managementRequestHandler;
    /** @var \App\Repository\MatchRepository */
    private $matchRepository;
    /** @var \App\Repository\PlayerRepository */
    private $playerRepository;


    public function __construct(
        LoggerInterface $logger,
        MatchManagementRequestHandler $managementRequestHandler,
        MatchRepository $matchRepository,
        PlayerRepository $playerRepository
    ) {
        $this->logger = $logger;
        $this->managementRequestHandler = $managementRequestHandler;
        $this->matchRepository = $matchRepository;
        $this->playerRepository = $playerRepository;
    }

    /**
     * Creation screen
     *
     * @Route("/list", name="match-management-list")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index() : Response
    {
        return $this->render(
            'match/list-not-played.html.twig',
            [
                'screenTitle' => 'Sélectionner un match à jouer',
                'matches' => $this->matchRepository->findNotDone()
            ]
        );
    }

    /**
     * @Route("/play-match/{id}", name="play-match")
     * @ParamConverter("match", class="App\Entity\Match")
     *
     * @param \App\Entity\Match $match
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function play(Match $match) : Response
    {
        if ($match->getDate()) {
            throw new NotFoundHttpException('Ce match a déjà été joué!');
        }

        $stadiums = $this->getDoctrine()
            ->getRepository(Stadium::class)
            ->findAll();

        return $this->render(
            'match/management.html.twig',
            [
                'screenTitle' => 'Gestion du match',
                'match' => $match,
                'stadiums' => $stadiums,
                'playersTeam1' => $this->playerRepository->getByTeam($match->getTeam1()->getId()),
                'playersTeam2' => $this->playerRepository->getByTeam($match->getTeam2()->getId()),
            ]
        );
    }

    /**
     * Tournament creation submission
     *
     * @Route("/match-management-submit", name="tournament-management-submit", methods={"POST"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function submitUpdate() : Response
    {
        $status = 'failure';
        try {
            $this->managementRequestHandler->process();
            $status = 'success';
        } catch (\Throwable $ex) {
            $this->logger->error($ex->getMessage() . ': ' . $ex->getTraceAsString());
        }

        return new JsonResponse(['status' => $status]);
    }
}
