<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            if ($this->getUser()->isVerified()) {
                return $this->redirectToRoute('app_profile');
            }
            return new Response("Votre adresse mail n'est pas vérifié");
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/verify/{verificationToken}', name: 'app_verify')]
    public function verify(string $verificationToken, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(["verificationToken" => $verificationToken]);

        if (!$user) {
            return new Response($verificationToken, 403);
        }

        $user->setVerified(true);
        return new Response("Votre adresse mail a bien été vérifiée. Vous pouvez vous connecter");
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
