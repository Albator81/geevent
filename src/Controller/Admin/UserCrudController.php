<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Uid\Uuid;

class UserCrudController extends AbstractCrudController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $isOwnProfile = false;
        if (Crud::PAGE_EDIT === $pageName) {
            $entity = $this->getContext()->getEntity()->getInstance();
            $currentUser = $this->getUser();
            if ($currentUser instanceof User && $entity->getId() === $currentUser->getId()) {
                $isOwnProfile = true;
            }
        }

        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('username'),
            TextField::new('password')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setFormTypeOption('mapped', false)
                ->setRequired(Crud::PAGE_NEW === $pageName),
            BooleanField::new('isAdmin')
                ->renderAsSwitch(false)
                ->setDisabled($isOwnProfile),
            TextField::new('email'),
            BooleanField::new('verified')
                ->renderAsSwitch(false),
        ];
    }

    private function handlePassword(User $user): void
    {
        $request = $this->getContext()->getRequest();
        $formData = $request->request->all('User');
        $plainPassword = $formData['password'] ?? null;

        // Si un mot de passe a été saisi, on le hash et on l'enregistre
        if (!empty($plainPassword)) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }
    }

    public function createEntity(string $entityFqcn)
    {
        $user = new User();
        $user->setVerificationToken(Uuid::v4());

        return $user;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handlePassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handlePassword($entityInstance);
        if ($entityInstance->isAdmin()) {
            $entityInstance->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        } else {
            $entityInstance->setRoles(['ROLE_USER']);
        }
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $currentUser = $this->getUser();
        if ($currentUser instanceof User && $currentUser->getId() === $entityInstance->getId()) {
            throw new AccessDeniedException('Vous ne pouvez pas supprimer votre propre compte administrateur.');
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }
}
