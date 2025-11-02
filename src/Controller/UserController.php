<?php

namespace App\Controller;

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
        //c'est un test pr l'avatar
        $user = $this->getUser();
        $avatar = $user->getAvatar() ? base64_encode(stream_get_contents($user->getAvatar())) : null;

        return $this->render('user/index.html.twig', [
            'avatar' => $avatar,
        ]);
    }
}
