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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EventsCrudController extends AbstractCrudController
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Events::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['title', 'description', 'address'])
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
                ->onlyOnForms()
                ->setHelp('Les coordonnées GPS seront automatiquement saisies si votre adresse est connue par nos services (OpenStreetMap Nominatim).'),
            NumberField::new('latitude')
                ->onlyOnForms()
                ->setRequired(false)
                ->setNumDecimals(6)
                ->setStoredAsString()
                ->setHelp('Si non remplie, votre événement ne sera pas affiché sur la carte.'),
            NumberField::new('longitude')
                ->onlyOnForms()
                ->setRequired(false)
                ->setNumDecimals(6)
                ->setStoredAsString()
                ->setHelp('Si non remplie, votre événement ne sera pas affiché sur la carte.'),
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->getAndSetCoord($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->getAndSetCoord($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function getAndSetCoord($entity): void
    {
        if (empty($entity->getAddress())) {
            return;
        }

        // !! Futur nous, l'api nous interdit de faire une fonction d'auto complete ou on l'appelle a chaque lettre tappée !!
        $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
            // Désactive la vérification SSL pour que ça marche sur localhost
            'verify_peer' => false, //comment that line on prod
            'query' => [
                'q' => $entity->getAddress(), //ici on donne l'adresse postal a l'API
                'format' => 'json', //format de la réponce, json est facile a transformé en array, et nous permet de mettre une limit de réponces, donc on l'utilise
                'limit' => 1 //ici on ne récupère que la localisation la plus pertinante, par exemple si il y a plusieurs résultats, seul le premier sera renvoyé
            ],
            'headers' => [
                'User-Agent' => 'GeeventApp/1.0 (set-contact-mail-for-prod@gmail.com)' // !! ici il faudra qu'on spécifie une adresse email si on mets le truc en prod, car si notre app fait trop de requète on risque le ban IP puisque les dev n'auront aucun moyen de nous contacté (max 1req/sec)
            ]
        ]);

        // Converti la réponse
        $data = $response->toArray();

        if (!empty($data)) {
            $entity->setLatitude($data[0]['lat']);
            $entity->setLongitude($data[0]['lon']);
        }
    }
}
