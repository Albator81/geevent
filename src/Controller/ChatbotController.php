<?php

namespace App\Controller;

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
    public function talk(Request $request, MistralApiService $mistralApiService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? null;

        if (!$userMessage) {
            return new JsonResponse(['error' => 'Message is empty'], 400);
        }

        try {
            $botResponse = $mistralApiService->getResponse($userMessage);
            return new JsonResponse(['response' => $botResponse]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue: ' . $e->getMessage()], 500);
        }
    }
}
