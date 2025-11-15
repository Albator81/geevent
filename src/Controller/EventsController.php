<?php

namespace App\Controller;

use App\Entity\Events;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EventsController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function index(EventsRepository $eventsRepository, Request $request): Response
    {
        $events = $eventsRepository->findBy([], ['start_datetime' => 'DESC']);

        $display = $request->query->get('d');

        if ('c' == $display) {
            return $this->render('events/carte.html.twig', [
                'events' => $events,
            ]);
        }

        return $this->render('events/index.html.twig', [
            'events' => $events,
        ]);
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
