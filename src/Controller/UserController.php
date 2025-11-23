<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/profile', name: 'app_profile')]
    public function index(Security $security): Response
    {
        if (!$this->getUser()->isVerified()) {
            $security->logout(false);
            $this->addFlash('danger', "Vous devez vérifier votre compte afin de vous connecter. Vérifiez vos mails");
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/index.html.twig');
    }
}
