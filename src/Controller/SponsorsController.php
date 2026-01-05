<?php

namespace App\Controller;

use App\Entity\Sponsors;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SponsorsRepository;

final class SponsorsController extends AbstractController
{
    #[Route('/sponsors', name: 'app_sponsors')]
    public function index(SponsorsRepository $sponsorsRepository): Response
    {
        return $this->render('sponsors/index.html.twig', [
            'controller_name' => 'SponsorsController',
            'sponsors' => $sponsorsRepository->findAll(),
        ]);
    }
}
