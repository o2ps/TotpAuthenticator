<?php

use Oops\TotpAuthenticator\Security\TotpAuthenticator;
use Oops\TotpAuthenticator\Utils\TimeProvider;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$googleAuthenticator = new TotpAuthenticator(new TimeProvider());
Assert::match(
	'~^otpauth://totp/jiripudil\?secret=[A-Z2-7]{32}$~',
	$googleAuthenticator->getTotpUri($googleAuthenticator->getRandomSecret(), 'jiripudil')
);

$googleAuthenticator = (new TotpAuthenticator(new TimeProvider()))->setIssuer('jiripudil.cz');
Assert::match(
	'~^otpauth://totp/jiripudil\.cz:jiripudil\?secret=[A-Z2-7]{32}&issuer=jiripudil\.cz$~',
	$googleAuthenticator->getTotpUri($googleAuthenticator->getRandomSecret(), 'jiripudil')
);

$googleAuthenticator = (new TotpAuthenticator(new TimeProvider()))->setIssuer('Great Issuer With Spaces');
Assert::match(
    '~^otpauth://totp/Great%20Issuer%20With%20Spaces:jiripudil\?secret=[A-Z2-7]{32}&issuer=Great%20Issuer%20With%20Spaces$~',
    $googleAuthenticator->getTotpUri($googleAuthenticator->getRandomSecret(), 'jiripudil')
);