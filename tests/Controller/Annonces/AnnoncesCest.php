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
        UserFactory::createOne(['isAdmin' => true]);
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
        UserFactory::createOne(['isAdmin' => true]);
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
        UserFactory::createOne(['isAdmin' => true]);
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

    /**
     * Teste qu'une annonce protégée redirige les utilisateurs anonymes
     */
    public function testShowAnnonceProtectedRedirectsAnonymous(ControllerTester $I): void
    {
        UserFactory::createOne(['isAdmin' => true]);
        $annonce = AnnoncesFactory::createOne([
            'sentTo' => Target::MEMBERS,
        ]);

        $I->amOnPage('/annonces/' . $annonce->getId());
        $I->seeCurrentRouteIs('app_home');
        $I->see('Vous devez vous connecter pour accéder à cette annonce.', '.alert-info');
    }

    /**
     * Teste qu'un utilisateur connecté peut voir une annonce protégée
     */
    public function testShowAnnonceProtectedShowsToLoggedInUser(ControllerTester $I): void
    {
        $user = UserFactory::createOne(['isAdmin' => true]);
        $annonce = AnnoncesFactory::createOne([
            'title' => 'Annonce pour membres',
            'content' => 'Contenu visible uniquement par les membres.',
            'sentTo' => Target::MEMBERS,
        ]);

        $I->amLoggedInAs($user->_real());
        $I->amOnPage('/annonces/' . $annonce->getId());
        $I->seeResponseCodeIs(200);
        $I->see('Annonce pour membres', 'h1');
        $I->see('Contenu visible uniquement par les membres.');
    }
}
