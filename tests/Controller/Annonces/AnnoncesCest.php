<?php

namespace App\Tests\Controller\Annonces;

use App\Factory\AnnoncesFactory;
use App\Factory\UserFactory;
use App\Tests\Support\ControllerTester;
use App\Enum\Target;

class AnnoncesCest
{
    /**
     * Teste l'affichage de la liste des annonces
     */
    public function testIndexPage(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        AnnoncesFactory::createMany(3);

        $I->amOnPage('/annonces');
        $I->seeResponseCodeIs(200);
        $I->see('Liste des annonces', 'h1');
        $I->seeNumberOfElements('.card', 3);
    }

    /**
     * Teste la pagination des annonces
     */
    public function testPagination(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        AnnoncesFactory::createMany(10);

        $I->amOnPage('/annonces');
        $I->seeResponseCodeIs(200);
        
        $I->seeNumberOfElements('.card', 6);
        
        $I->see('Suivant');
        $I->click('Suivant');
        
        $I->seeInCurrentUrl('page=1');
        $I->seeNumberOfElements('.card', 4);
    }

    /**
     * Teste l'affichage de la page des détails d'une annonce publique
     */
    public function testShowAnnoncePublic(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        $annonce = AnnoncesFactory::createOne([
            'title' => 'Annonce publique',
            'content' => 'Contenu visible par tous.',
            'sentTo' => Target::ALL,
        ]);

        $I->amOnPage('/annonces/' . $annonce->getId());
        $I->seeResponseCodeIs(200);
        $I->see('Annonce publique', 'h1');
        $I->see('Contenu visible par tous.');
    }
}
