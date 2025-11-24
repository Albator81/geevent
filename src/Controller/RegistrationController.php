<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setIsAdmin(false);
            $user->setVerified(false);
            $user->setVerificationToken(UUID::v4());

            $entityManager->persist($user);
            $entityManager->flush();

            $e = $user->getEmail();

            $email = (new TemplatedEmail())
                ->from('test@test.com')
                ->to($e)
                ->subject('Vérification Email GeEvent')
                ->htmlTemplate('registration/email.html.twig')
                ->context(["user" => $user]);

            $mailer->send($email);
            
            $this->addFlash("info", "Un lien de vérification a été envoyé a l'adresse $e, veuillez cliquer sur le lien pour vérifier votre compte");
            return $this->redirectToRoute("app_login");
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
