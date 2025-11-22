<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('test@test.com')
            ->to('adam.benahmeed@gmail.com')
            ->subject('sujet ?')
            ->text('ça a l air de marcher')
            ->html('<h1>WOW</h1><br><br>ca marche ?');

        $mailer->send($email);
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
