<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MistralApiService
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(
        HttpClientInterface $client,
        #[Autowire(env: 'CHATBOT_API_KEY')] string $apiKey
    ) {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function getResponse(string $userMessage): string
    {
        $systemPrompt = "Tu es l'assistant virtuel de l'association Sacrée Tech et de la plateforme Ge'Event. " .
            "Ta mission est d'aider les utilisateurs concernant les événements (Meetups, Apéros) et la vie de l'association à Reims. " .
            "Tu ne dois JAMAIS parler de politique, de religion, ou de sujets controversés. " .
            "Si une question sort de ton domaine d'expertise (l'association, la tech à Reims, l'application Ge'Event), refuse poliment d'y répondre. " .
            "Sois concis, serviable et toujours poli. Réponds en français.";

        $response = $this->client->request('POST', 'https://api.mistral.ai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'mistral-tiny', // Using a lightweight model
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => 0.7,
            ],
        ]);

        $content = $response->toArray();

        return $content['choices'][0]['message']['content'] ?? "Désolé, je n'ai pas pu traiter votre demande.";
    }
}
