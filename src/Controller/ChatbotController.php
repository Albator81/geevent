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
        AnnoncesRepository $annoncesRepository,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? null;

        if (!$userMessage) {
            return new JsonResponse(['error' => 'Message is empty'], 400);
        }

        $session = $request->getSession();
        $history = $session->get('chatbot_history', []);

        $history[] = ['role' => 'user', 'content' => $userMessage];

        $events = $eventsRepository->betterFindAll('', 0, 50);
        $annonces = $annoncesRepository->findAll();
        $sponsors = $sponsorsRepository->findAll();
        $context = 'Date actuelle : '.(new \DateTime())->format('d/m/Y H:i')."\n\n";

        foreach ($events as $event) {
            $context .= '- Id : '.$event['id'].' Titre : '.$event['title'].' ('.$event['type']->name.') : '.
                $event['description'].'. Le '.$event['start_datetime']->format('d/m/Y à H:i').
                ' à '.$event['address'].".\n";
        }

        $context .= "\nDernières annonces de l'association :\n";
        foreach ($annonces as $annonce) {
            $context .= '- ['.$annonce->getSentAt()->format('d/m/Y').'] '.$annonce->getTitle().' : '.$annonce->getContent()."\n";
        }

        $context .= "\nSponsors de l'association :\n";
        foreach ($sponsors as $sponsor) {
            $context .= '- '.$sponsor->getName().' ('.($sponsor->getWebsiteUrl() ?? 'Pas de site').")\n";
        }

        try {
            $botResponse = $mistralApiService->getResponse($history, $context);

            $history[] = ['role' => 'assistant', 'content' => $botResponse];

            $session->set('chatbot_history', $history);

            return new JsonResponse(['response' => $botResponse]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue'], 500);
        }
    }

    #[Route('/chatbot/history', name: 'app_chatbot_history')]
    public function history(Request $request): JsonResponse
    {
        $history = $request->getSession()->get('chatbot_history', []);

        return new JsonResponse([
            'history' => $history,
        ]);
    }

    #[Route('/chatbot/reset', name: 'app_chatbot_reset')]
    public function reset(Request $request): JsonResponse
    {
        $request->getSession()->remove('chatbot_history');

        return new JsonResponse(['status' => 'History cleared']);
    }
}
