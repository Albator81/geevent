<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EventsRepository $eventsRepo, AnnoncesRepository $annoncesRepo): Response
    {
        return $this->render('home/index.html.twig', [
            'upcomingEvents' => $eventsRepo->findUpcoming(3),

            'latestAnnonces' => $annoncesRepo->findLatestPublic(5),
        ]);
    }

    #[Route('/cgu', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('home/cgu.html.twig');
    }
}
