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
}