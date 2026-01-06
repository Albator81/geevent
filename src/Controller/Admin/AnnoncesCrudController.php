<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Enum\Target;

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
            ChoiceField::new('sent_to', 'Visible To')
                ->setChoices([
                    'Everyone' => Target::ALL,
                    'Members Only' => Target::MEMBERS,
                ])
                ->formatValue(function ($value) {
                    if ($value === Target::MEMBERS) {
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
}
