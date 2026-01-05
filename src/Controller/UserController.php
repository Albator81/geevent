<?php

namespace App\Controller;

use App\Entity\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/profil/unsubscribe/{id}', name: 'app_user_unsubscribe')]
    #[IsGranted('ROLE_USER')]
    public function Sedesincrire(Events $event, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $user->removeEventsJoined($event);

        $em->flush();

        $this->addFlash('success', 'Vous vous êtes désinscrit de l\'événement.');

        return $this->redirectToRoute('app_user_profile');
    }
}
