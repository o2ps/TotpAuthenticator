<?php

use Oops\GoogleAuthenticator\Security\GoogleAuthenticator;
use Oops\GoogleAuthenticator\Utils\TimeProvider;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$googleAuthenticator = new GoogleAuthenticator(new TimeProvider);
Assert::match('~^[A-Z2-7]{32}$~', $seed1 = $googleAuthenticator->getRandomSecret());
Assert::notEqual($googleAuthenticator->getRandomSecret(), $seed1);
