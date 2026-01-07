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
            ImageField::new('cover')
                ->onlyOnForms()
                // Ici on spécifie le dossier où EasyAdmin va stocker les fichiers car il ne seront pas dans le chemin par défaut "public/uploads/images"
                ->setUploadDir('public/uploads/events')
                ->setBasePath('uploads/events')
                // Ici on renomme le fichier pour éviter les doublons
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired(false),
            TextEditorField::new('description')
                ->onlyOnForms(),
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
            DateTimeField::new('start_datetime')
                ->setFormat('dd/MM/yyyy'),
            DateTimeField::new('end_datetime')
                ->setFormat('dd/MM/yyyy'),
            TextField::new('created_by')
                ->onlyOnIndex(),
            TextField::new('address')
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
            AssociationField::new('sponsors')
                ->autocomplete()
                // Cette ligne permet que lédition des participants/sponsors soit fonctionnel
                ->setFormTypeOption('by_reference', false)
                ->setHelp('<strong>Recherche disponible uniquement par <b>Nom</b>.</strong>'),
            AssociationField::new('participants')
                ->autocomplete()
                // Cette ligne permet que lédition des participants/sponsors soit fonctionnel
                ->setFormTypeOption('by_reference', false)
                ->setHelp('<strong>Recherche disponible uniquement par <b>Username</b>.</strong>'),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $event = new Events();
        $event->setCreatedBy($this->getUser());
        return $event;
    }
}
