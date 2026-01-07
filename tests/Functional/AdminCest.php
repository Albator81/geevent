<?php

declare(strict_types=1);


namespace Tests\Functional;

use Tests\Support\FunctionalTester;
use App\Factory\UserFactory;

final class AdminCest
{
    public function _before(FunctionalTester $I): void
    {
        // Code here will be executed before each test.
    }

    public function tryLoggedInAccessAdmin(FunctionalTester $I): void
    {
        $user = UserFactory::createOne([
            "username" => "Testing", 
            "email" => "test@gmail.com",
            "password" => "passwordtest",
        ]);

        $I->amLoggedInAs($user->_real());
        $I->amOnPage("/admin");
        $I->seeResponseCodeIs(403);

    }
}
