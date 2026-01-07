<?php

namespace App\Controller;

use App\Entity\Sponsors;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SponsorsRepository;

final class SponsorsController extends AbstractController
{
    #[Route('/sponsors', name: 'app_sponsors')]
    public function index(SponsorsRepository $sponsorsRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 8;
        $sponsors = $sponsorsRepository->findPaginatedSponsors($page, $limit);
        $totalSponsors = count($sponsors);
        $maxPage = ceil($totalSponsors / $limit);

        if ($page > $maxPage && $maxPage > 0) {
            return $this->redirectToRoute('app_sponsors', ['page' => $maxPage]);
        }

        return $this->render('sponsors/index.html.twig', [
            'controller_name' => 'SponsorsController',
            'sponsors' => $sponsors,
            'currentPage' => $page,
            'maxPage' => $maxPage,
        ]);
    }
    #[Route('/sponsors/{id}', name: 'app_sponsors_show', methods: ['GET'])]
    public function show(Sponsors $sponsor): Response
    {
        return $this->render('sponsors/show.html.twig', [
            'sponsor' => $sponsor,
        ]);
    }
}
