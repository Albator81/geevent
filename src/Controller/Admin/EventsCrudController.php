<?php

namespace App\Controller\Admin;

use App\Entity\Events;
use App\Enum\Type;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Events::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('description'),
            DateTimeField::new('start_datetime'),
            DateTimeField::new('end_datetime'),
            TextField::new('address'),

            ChoiceField::new('type')
                ->setChoices([
                    'Meetup' => Type::MEETUP,
                    'Apéro' => Type::APERO,
                ])
                ->renderAsNativeWidget(),

            TextField::new('created_by.username', 'Créateur'),
        ];
    }
}
