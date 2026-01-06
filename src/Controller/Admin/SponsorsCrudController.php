<?php

namespace App\Controller\Admin;

use App\Entity\Sponsors;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\QueryBuilder;

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
                // Ici on spécifie le préfixe de l'URL pour afficher l'image (chemin relatif depuis /public)
                ->setBasePath('uploads/sponsors')
                // Ici on renomme le fichier pour éviter les doublons
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired(false),
            UrlField::new('website_url'),
            AssociationField::new('event', 'Sponsored Events')
                ->autocomplete()
                //Tri du plus récent au plus vieu
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder): QueryBuilder => $queryBuilder->orderBy('entity.start_datetime', 'DESC')
                ),
        ];
    }
}
