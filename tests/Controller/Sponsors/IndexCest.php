<?php

namespace App\Tests\Controller\Sponsors;

use App\Factory\SponsorsFactory;
use App\Tests\Support\ControllerTester;

class IndexCest
{
    public function listShowsSponsors(ControllerTester $I): void
    {
        SponsorsFactory::createMany(5, ['name' => 'Sponsor Test']);

        $I->amOnPage('/sponsors');
        $I->seeResponseCodeIsSuccessful();
        $I->seeInTitle('Nos Sponsors');
        $I->see('Ils nous soutiennent', 'h1');
        $I->seeNumberOfElements('.sponsor-card', 5);
        $I->see('Sponsor Test', '.sponsor-name');
    }

    public function paginationWorks(ControllerTester $I): void
    {
        SponsorsFactory::createMany(10);

        $I->amOnPage('/sponsors?page=1');
        $I->seeNumberOfElements('.sponsor-card', 8);
        $I->see('Page 1 / 2', '.pagination-container');

        $I->click('Suivant');
        $I->seeInCurrentUrl('page=2');
        $I->seeNumberOfElements('.sponsor-card', 2);
    }

    public function emptyListShowsMessage(ControllerTester $I): void
    {
        $I->amOnPage('/sponsors');
        $I->see("Aucun sponsor n'est enregistré pour le moment.");
    }
}