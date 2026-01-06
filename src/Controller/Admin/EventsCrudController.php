<?php

namespace App\Controller\Admin;

use App\Entity\Events;
use App\Enum\Type;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class EventsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Events::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['title', 'description'])
            ->setDefaultSort(['type' => 'ASC', 'start_datetime' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('description')
                ->onlyOnForms(),
            NumberField::new('latitude')
                ->onlyOnForms()
                ->setRequired(false)
                ->setNumDecimals(6)
                ->setStoredAsString(),
            NumberField::new('longitude')
                ->onlyOnForms()
                ->setRequired(false)
                ->setNumDecimals(6)
                ->setStoredAsString(),
            DateTimeField::new('start_datetime')
                ->setFormat('dd/MM/yyyy'),
            DateTimeField::new('end_datetime')
                ->setFormat('dd/MM/yyyy'),

            TextField::new('created_by')
                ->onlyOnIndex(),

            //AssociationField::new('sponsors')
            //    ->autocomplete()
            //    ->setHelp('<strong>Recherche disponible uniquement par <b>Titre</b> ou <b>Description</b> (le type et la date ne sont pas recherchables).</strong>'),

            ChoiceField::new('type')
                ->setChoices([
                    'Meetup' => Type::MEETUP,
                    'Apéro' => Type::APERO,
                ])
                ->formatValue(function ($value) {
                    if ($value === Type::MEETUP) {
                        return 'Meetup';
                    }
                    return 'Apéro';
                }),

            TextField::new('address')
                ->onlyOnForms(),

            AssociationField::new('participants')
                ->autocomplete()
                ->setHelp('<strong>Recherche disponible uniquement par <b>Titre</b> ou <b>Description</b> (le type et la date ne sont pas recherchables).</strong>'),

            //ImageField::new('cover')
            //    ->onlyOnForms(),
        ];
    }
}
