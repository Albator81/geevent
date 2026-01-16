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
        UserFactory::createMany(5);
        AnnoncesFactory::createMany(3);

        $I->amOnPage('/annonces');
        $I->seeResponseCodeIs(200);
        $I->see('Liste des annonces', 'h1');
        $I->seeNumberOfElements('.card', 3);
    }
}
