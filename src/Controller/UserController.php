<?php

namespace App\Controller;

use App\Entity\Events;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        $futureEvents = [];
        $pastEvents = [];
        $now = new \DateTime();

        foreach ($user->getEventsJoined() as $event) {
            if ($event->getStartDatetime() >= $now) {
                $futureEvents[] = $event;
            } else {
                $pastEvents[] = $event;
            }
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'futureEvents' => $futureEvents,
            'pastEvents' => $pastEvents,
        ]);
    }

    #[Route('/events/{id<\d+>}/leave', name: 'app_events_leave')]
    #[IsGranted('ROLE_USER')]
    public function Sedesincrire(Events $event, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $user->removeEventsJoined($event);

        $em->flush();

        $this->addFlash('success', 'Vous vous êtes désinscrit de l\'événement.');

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/profil/edit', name: 'app_user_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès !');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
