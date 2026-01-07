<?php

namespace App\Controller;

use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AnnoncesController extends AbstractController
{
    #[Route('/annonces', name: 'app_annonces')]
    public function index(
        AnnoncesRepository $annoncesRepository, 
        #[MapQueryParameter] int $page = 0 // Récupère le paramètre ?page= dans l'URL
    ): Response {
        if ($page < 0) $page = 0;

        $annonces = $annoncesRepository->betterFindAll($page * 6, 6);

        return $this->render('annonces/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }
}
