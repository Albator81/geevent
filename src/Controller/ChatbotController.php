<?php

namespace App\Controller;

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
        SponsorsRepository $sponsorsRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? null;

        if (!$userMessage) {
            return new JsonResponse(['error' => 'Message is empty'], 400);
        }

        // Fetch context data
        $upcomingEvents = $eventsRepository->createQueryBuilder('e')
            ->where('e.start_datetime >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.start_datetime', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $sponsors = $sponsorsRepository->findAll();

        $context = "Date actuelle : " . (new \DateTime())->format('d/m/Y H:i') . "\n\n";

        $context .= "Prochains événements :\n";
        foreach ($upcomingEvents as $event) {
            $context .= "- " . $event->getTitle() . " (" . $event->getType()->name . ") : " .
                $event->getDescription() . ". Le " . $event->getStartDatetime()->format('d/m/Y à H:i') .
                " à " . $event->getAddress() . ".\n";
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
