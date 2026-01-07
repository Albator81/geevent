<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\User;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EventsController extends AbstractController
{
    /**
     * Liste les évènements avec options de recherche et d'affichage (liste ou carte).
     */
    #[Route('/events', name: 'app_events')]
    public function index(
        EventsRepository $eventsRepository,
        #[MapQueryParameter] string $display = '', // Récupère le paramètre ?display= dans l'URL
        #[MapQueryParameter] string $search = '',  // Récupère le paramètre ?search= dans l'URL
        #[MapQueryParameter] int $page = 0,         // Récupère le paramètre ?page= dans l'URL
    ): Response {
        if ($page < 0) {
            $page = 0;
        }

        // Si le mode d'affichage est 'c' (carte), on utilise un template spécifique
        if ('c' === $display) {
            $events = $eventsRepository->betterFindAll($search);

            return $this->render('events/carte.html.twig', [
                'events' => $events,
                'search' => $search,
            ]);
        }

        $events = $eventsRepository->betterFindAll($search, $page * 6, 6);

        // Par défaut, rendu de la liste classique
        return $this->render('events/index.html.twig', [
            'events' => $events,
            'search' => $search,
        ]);
    }

    /**
     * Endpoint API pour la recherche d'évènements (retourne du JSON).
     */
    #[Route('/events/search', name: 'app_events_search')]
    public function search(EventsRepository $eventsRepository, #[MapQueryParameter] string $search = ''): Response
    {
        // Récupère les 4 premiers résultats correspondant à la recherche
        $events = $eventsRepository->betterFindAll($search, 0, 4);

        // Transformation des objets/tableaux en format JSON propre
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
                'participants_count' => (int) $event['participants_count'],
            ];
        }, $events);

        return new JsonResponse($data);
    }

    /**
     * Affiche les détails d'un évènement spécifique.
     * Utilise le ParamConverter pour transformer l'ID en objet Events.
     */
    #[Route('/events/{id<\d+>}', name: 'app_events_show')]
    public function show(Events $event): Response
    {
        $hasJoined = null;
        $user = $this->getUser();

        // Vérifie si l'utilisateur connecté participe déjà à l'évènement
        if ($user) {
            $hasJoined = $event->getParticipants()->contains($user);
        }

        return $this->render('events/show.html.twig', [
            'event' => $event,
            'hasJoined' => $hasJoined,
        ]);
    }

    /**
     * Récupère et affiche l'image de couverture stockée en BLOB/Stream dans la base de données.
     */
    #[Route('/events/{id<\d+>}/cover', name: 'app_events_cover')]
    public function getCover(Events $event): Response
    {
        // Récupère le contenu binaire de l'image
        $coverData = stream_get_contents($event->getCover());
        $response = new Response($coverData);

        // Détection automatique du type MIME (image/png, image/jpeg, etc.)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($coverData) ?: 'application/octet-stream';

        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

    /**
     * Permet à un utilisateur connecté de rejoindre un évènement.
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/events/{id<\d+>}/join', name: 'app_events_join')]
    public function join(Events $event, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $now = new \DateTime();

        // Sécurité : Impossible de rejoindre si l'évènement est déjà fini
        if ($event->getEndDatetime() < $now) {
            $this->addFlash('danger', "Impossible de rejoindre : l'évènement est terminé !");

            return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
        }

        if ($user instanceof User && !$user->isVerified()) {
            $this->addFlash('danger', "Impossible de rejoindre : Votre email n'est pas vérifié");

            return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
        }

        // Ajout du participant s'il n'est pas déjà inscrit
        if ($event->getParticipants()->contains($user)) {
            $this->addFlash('danger', "Vous avez déjà rejoint l'évènement !");
        } else {
            $event->addParticipant($user);
            $em->flush(); // Sauvegarde en base de données
            $this->addFlash('success', "Vous avez bien rejoint l'évènement !");
        }

        return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
    }

    /**
     * Permet à un utilisateur de se désinscrire d'un évènement.
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/events/{id<\d+>}/leave', name: 'app_events_leave')]
    public function leave(Events $event, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $now = new \DateTime();

        // Sécurité : On ne peut plus quitter un évènement terminé (logique métier)
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
