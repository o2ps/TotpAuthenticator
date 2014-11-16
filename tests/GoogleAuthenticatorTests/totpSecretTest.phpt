<?php

use Oops\GoogleAuthenticator\Security\GoogleAuthenticator;
use Oops\GoogleAuthenticator\Utils\TimeProvider;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$googleAuthenticator = new GoogleAuthenticator(new TimeProvider);
Assert::match(
	'~^otpauth://totp/jiripudil\?secret=[A-Z2-7]{32}$~',
	$googleAuthenticator->getTotpUri($googleAuthenticator->getRandomSecret(), 'jiripudil')
);

$googleAuthenticator = (new GoogleAuthenticator(new TimeProvider))->setIssuer('jiripudil.cz');
Assert::match(
	'~^otpauth://totp/jiripudil\.cz:jiripudil\?secret=[A-Z2-7]{32}&issuer=jiripudil\.cz$~',
	$googleAuthenticator->getTotpUri($googleAuthenticator->getRandomSecret(), 'jiripudil')
);
