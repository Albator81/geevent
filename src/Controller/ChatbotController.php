<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use App\Repository\EventsRepository;
use App\Repository\SponsorsRepository;
use App\Service\MistralApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{
    #[Route('/chatbot', name: 'app_chatbot')]
    public function index(): Response
    {
        return $this->render('chatbot/index.html.twig');
    }

    #[Route('/chatbot/talk', name: 'app_chatbot_talk', methods: ['POST'])]
    public function talk(
        Request $request,
        MistralApiService $mistralApiService,
        EventsRepository $eventsRepository,
        SponsorsRepository $sponsorsRepository,
        AnnoncesRepository $annoncesRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? null;

        if (!$userMessage) {
            return new JsonResponse(['error' => 'Message is empty'], 400);
        }

        $events = $eventsRepository->betterFindAll('');

        $annonces = $annoncesRepository->findAll();
        $sponsors = $sponsorsRepository->findAll();

        $context = "Date actuelle : " . (new \DateTime())->format('d/m/Y H:i') . "\n\n";

        $context .= "Prochains événements :\n";
        
        foreach ($events as $event) {
            $context .= "- Id : " . $event["id"]. " Titre : " . $event["title"] . " (" . $event["type"]->name . ") : " .
                $event["description"] . ". Le " . $event["start_datetime"]->format('d/m/Y à H:i') .
                " à " . $event["address"] . ".\n";
        }

        $context .= "\nDernières annonces de l'association :\n";
        foreach ($annonces as $annonce) {
            $context .= "- [" . $annonce->getSentAt()->format('d/m/Y') . "] " . $annonce->getTitle() . " : " . $annonce->getContent() . "\n";
        }

        $context .= "\nSponsors de l'association :\n";
        foreach ($sponsors as $sponsor) {
            $context .= "- " . $sponsor->getName() . " (" . ($sponsor->getWebsiteUrl() ?? 'Pas de site') . ")\n";
        }

        try {
            $botResponse = $mistralApiService->getResponse($userMessage, $context);
            return new JsonResponse(['response' => $botResponse]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue: ' . $e->getMessage()], 500);
        }
    }
}
