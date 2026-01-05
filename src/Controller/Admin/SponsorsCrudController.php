<?php

namespace App\Controller\Admin;

use App\Entity\Sponsors;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            ImageField::new('logo')
                // 1. Dossier où EasyAdmin va stocker les fichiers (chemin physique)
                // Créez ce dossier : public/uploads/sponsors
                ->setUploadDir('public/uploads/sponsors')

                // 2. Préfixe de l'URL pour afficher l'image (chemin relatif depuis /public)
                ->setBasePath('uploads/sponsors')

                // 3. (Optionnel) Renomme le fichier pour éviter les doublons
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            UrlField::new('website_url'),
        ];
    }
}
