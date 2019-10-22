<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       13/09/2019 (dd-mm-YYYY)
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Index extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(): Response
    {
        return $this->render(
            'home/home.html.twig',
            ['screenTitle' => 'Accueil']
        );
    }
}
