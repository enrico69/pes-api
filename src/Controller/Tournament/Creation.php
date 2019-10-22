<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       14/09/2019 (dd-mm-YYYY)
 */

namespace App\Controller\Tournament;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Service\Tournament\TournamentFormCreationRequestHandler;

class Creation extends AbstractController
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \App\Service\Tournament\TournamentFormCreationRequestHandler */
    private $formCreationRequestHandler;

    public function __construct(LoggerInterface $logger, TournamentFormCreationRequestHandler $formCreationRequestHandler)
    {
        $this->logger = $logger;
        $this->formCreationRequestHandler = $formCreationRequestHandler;
    }

    /**
     * Creation screen.
     *
     * @Route("/tournament-creation", name="tournament-creation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(): Response
    {
        return $this->render(
            'tournament/creation.html.twig',
            ['screenTitle' => 'Créer un tournoi']
        );
    }

    /**
     * Tournament creation submission.
     *
     * @Route("/tournament-creation-submit", name="tournament-creation-submit", methods={"POST"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function submitCreation(): Response
    {
        $status = 'failure';
        try {
            $this->formCreationRequestHandler->process();
            $status = 'success';
        } catch (\Throwable $ex) {
            $this->logger->error($ex->getMessage().': '.$ex->getTraceAsString());
        }

        return new JsonResponse(['status' => $status]);
    }
}
