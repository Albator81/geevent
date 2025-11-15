<?php

namespace App\Controller;

use App\Entity\Sponsors;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SponsorsController extends AbstractController
{
    #[Route('/sponsors', name: 'app_sponsors')]
    public function index(): Response
    {
        return $this->render('sponsors/index.html.twig', [
            'controller_name' => 'SponsorsController',
        ]);
    }

    #[Route('/sponsors/{id<\d+>}/logo', name: 'app_sponsors_logo')]
    public function getLogo(Sponsors $sponsor): Response
    {
        $logoData = stream_get_contents($sponsor->getLogo());
        $response = new Response($logoData);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($logoData) ?: 'application/octet-stream';

        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }
}
