<?php

namespace App\Controller;

use App\Entity\Events;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;

final class EventsController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function index(EventsRepository $eventsRepository, #[MapQueryParameter] string $display = '', #[MapQueryParameter] string $search = ''): Response
    {
        $events = $eventsRepository->betterFindAll($search);

        if ('c' === $display) {
            return $this->render('events/carte.html.twig', [
                'events' => $events,
                'search' => $search,
            ]);
        }

        // dd($search);
        return $this->render('events/index.html.twig', [
            'events' => $events,
            'search' => $search,
        ]);
    }

    #[Route('/events/search', name: 'app_events_search')]
    public function search(EventsRepository $eventsRepository, #[MapQueryParameter] string $search = ''): Response {

        $events = $eventsRepository->betterFindAll($search, 0, 4);

        // c moche
        $data = array_map(function (array $event): array {
            return [
                'id' => (int) $event['id'],
                'title' => $event['title'],
                'description' => $event['description'],
                'start_datetime' => $event['start_datetime'],
                'end_datetime' => $event['end_datetime'],
                'longitude' => (float) $event['longitude'],
                'latitude' => (float) $event['latitude'],
                'type' => $event['type'],
                'address' => $event['address'],
                'creator_username' => $event['creator_username'],
                'sponsors_count' => (int) $event['sponsors_count'],
                'participants_count'=> (int) $event['participants_count'],
            ];
        }, $events);

        return new JsonResponse($data);
    }

    #[Route('/events/{id<\d+>}', name: 'app_events_show')]
    public function show(Events $event): Response
    {
        $hasJoined = null;

        $user = $this->getUser();

        if ($user) {
            $hasJoined = $event->getParticipants()->contains($user);
        }

        return $this->render('events/show.html.twig', [
            'event' => $event,
            'hasJoined' => $hasJoined,
        ]);
    }

    #[Route('/events/{id<\d+>}/cover', name: 'app_events_cover')]
    public function getCover(Events $event): Response
    {
        $coverData = stream_get_contents($event->getCover());
        $response = new Response($coverData);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($coverData) ?: 'application/octet-stream';

        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/events/{id<\d+>}/join', name: 'app_events_join')]
    public function join(Events $event, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $now = new \DateTime();

        if ($event->getEndDatetime() < $now) {
            $this->addFlash('danger', "Impossible de rejoindre : l'évènement est terminé !");

            return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
        }

        if ($user instanceof User && !$user->isVerified()) {
            $this->addFlash('danger', "Impossible de rejoindre : Votre email n'est pas vérifié");

            return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
        }

        if ($event->getParticipants()->contains($user)) {
            $this->addFlash('danger', "Vous avez déjà rejoint l'évènement !");
        } else {
            $event->addParticipant($user);
            $em->flush();

            $this->addFlash('success', "Vous avez bien rejoint l'évènement !");
        }

        return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/events/{id<\d+>}/leave', name: 'app_events_leave')]
    public function leave(Events $event, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $now = new \DateTime();

        if ($event->getEndDatetime() < $now) {
            $this->addFlash('danger', "Impossible de quitter : l'évènement est terminé !");

            return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
        }

        if ($event->getParticipants()->contains($user)) {
            $event->removeParticipant($user);
            $em->flush();

            $this->addFlash('success', "Vous avez quitté l'évènement avec succès !");
        } else {
            $this->addFlash('danger', "Vous n'avez pas rejoint cet évènement !");
        }

        return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
    }
}
