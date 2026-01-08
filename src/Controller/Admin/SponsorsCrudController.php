<?php

namespace App\Controller\Admin;

use App\Entity\Sponsors;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class SponsorsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sponsors::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('name'),
            ImageField::new('logoPath', 'Logo')
                // Ici on spécifie le dossier où EasyAdmin va stocker les fichiers car il ne seront pas dans le chemin par défaut "public/uploads/images"
                ->setUploadDir('public/uploads/sponsors')
                ->setBasePath('uploads/sponsors')
                // Ici on renomme le fichier pour éviter les doublons
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired(false),
            UrlField::new('website_url')->setHelp('<strong>Votre url doit être sous a forme: https://example.com</strong>'),
            FormField::addPanel(''),
            AssociationField::new('event', 'Sponsored Events')
                ->autocomplete()
                ->setHelp('<strong>Recherche disponible uniquement par <b>Titre</b>, <b>Description</b> et <b>Adresse</b> (le type et la date ne sont pas recherchables).</strong>')
        ];
    }
}
