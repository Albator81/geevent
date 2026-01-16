<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use App\Enum\Target;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class AnnoncesCrudController extends AbstractCrudController
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Annonces::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('content')
                ->onlyOnForms(),
            ChoiceField::new('sent_to', 'Visible To')
                ->setChoices([
                    'Everyone' => Target::ALL,
                    'Members Only' => Target::MEMBERS,
                ])
                ->formatValue(function ($value) {
                    if (Target::MEMBERS === $value) {
                        return 'Members Only';
                    }

                    return 'Everyone';
                }),

            TextField::new('sent_by')
                ->onlyOnIndex(),
            DateField::new('sent_at')
                ->setFormat('dd/MM/yyyy')
                ->onlyOnIndex(),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $annonce = new Annonces();
        $annonce->setSentBy($this->getUser());
        $annonce->setSentAt(new \DateTime());
        $annonce->setSentTo(Target::ALL);

        return $annonce;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->sendAnnouncementEmail($entityInstance);
    }

    private function sendAnnouncementEmail(Annonces $annonce): void
    {
        $users = $this->userRepository->findBy(['verified' => true]);

        $recipients = [];
        foreach ($users as $user) {
            $recipients[] = $user->getEmail();
        }

        if (empty($recipients)) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from('test@test.com')
            ->bcc(...$recipients)
            ->subject('📢 Nouvelle annonce : '.$annonce->getTitle())
            ->htmlTemplate('annonces/email.html.twig')
            ->context([
                'annonce' => $annonce,
                'user' => $this->getUser(),
            ]);

        $this->mailer->send($email);
    }
}
