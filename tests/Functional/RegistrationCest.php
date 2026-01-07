<?php

declare(strict_types=1);

namespace Tests\Functional;

use Tests\Support\FunctionalTester;

final class RegistrationCest
{
    public function _before(FunctionalTester $I): void
    {
        // Code here will be executed before each test function.
        $I->amGoingTo("/");
    }

    // All `public` methods will be executed as tests.
    public function tryRegister(FunctionalTester $I): void
    {
        $I->amOnPage("/");
        $I->click("Inscription");
        $I->submitForm("form", [
            "username" => "Testing123", 
            "email" => "baaaaa@gmail.com", 
            "plainPassword" => "passwordtest",
            "agreeTerms" => "1"
        ]);
        // $I->fillField("username","Testing123");
        // $I->fillField("email","baaaa@gmail.com");
        // $I->fillField("plainPassword","passwordTest");
        // $I->checkOption("agreeTerms");
        $I->seeEmailIsSent();
    }
}
