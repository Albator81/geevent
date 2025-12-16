<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PhpParser\Node\Expr\Cast\Bool_;
use Doctrine\ORM\EntityManagerInterface;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('username'),
            BooleanField::new('isAdmin')
                ->renderAsSwitch(false),
            TextField::new('email'),
            BooleanField::new('verified')
                ->renderAsSwitch(false),
        ];
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->isAdmin()) {
            $entityInstance->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        } else {
            $entityInstance->setRoles(['ROLE_USER']);
        }
        parent::updateEntity($entityManager, $entityInstance);
    }
}