<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AnnoncesCrudController extends AbstractCrudController
{
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
            BooleanField::new('isMembersOnly', 'Members Only')
                ->renderAsSwitch(false)
                ->setHelp('Visible to everyone unless checked.'),
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
        $annonce->setIsMembersOnly(false);
        return $annonce;
    }
}
