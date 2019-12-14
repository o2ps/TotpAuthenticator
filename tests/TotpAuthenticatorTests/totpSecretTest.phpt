<?php declare(strict_types = 1);

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

$googleAuthenticator = (new TotpAuthenticator(new TimeProvider()))->setIssuer('Jiří Pudil');
Assert::match(
	'~^otpauth://totp/Ji%C5%99%C3%AD%20Pudil:Ji%C5%99%C3%AD%20Pudil\?secret=[A-Z2-7]{32}&issuer=Ji%C5%99%C3%AD%20Pudil$~',
	$googleAuthenticator->getTotpUri($googleAuthenticator->getRandomSecret(), 'Jiří Pudil')
);
