<?php

namespace App\Controller;

use App\Enum\Target;
use App\Entity\Annonces;
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

    /**
     * Affiche les détails d'une annonce specifique
     */
    #[Route('/annonces/{id<\d+>}', name: 'app_annonces_show')]
    public function show(Annonces $annonce): Response
    {
        if ($annonce->getSentTo() === Target::ALL) {
            return $this->render('annonces/show.html.twig', [
                'annonce' => $annonce,
            ]);
        }

        $user = $this->getUser();
        if ($user === null) return $this->redirectToRoute('app_home');

        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}
