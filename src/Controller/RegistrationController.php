<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
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

            $email = (new TemplatedEmail())
                ->from('test@test.com')
                ->to('adam.benahmeed@gmail.com')
                ->subject('sujet ?')
                ->htmlTemplate('registration/email.html.twig')
                ->context(["token" => $user->getVerificationToken(), "username" => $user->getUsername()]);

            $mailer->send($email);

            return new Response("Vreifiez vos mails");
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
