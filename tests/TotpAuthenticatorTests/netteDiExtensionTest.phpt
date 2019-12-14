<?php declare(strict_types = 1);

use Nette\DI\Compiler;
use Oops\TotpAuthenticator\DI\TotpAuthenticatorExtension;
use Oops\TotpAuthenticator\Security\TotpAuthenticator;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$containerLoader = new \Nette\DI\ContainerLoader(sys_get_temp_dir() . '/nette.configurator', true);
$containerClassName = $containerLoader->load(static function (Compiler $compiler): void {
	$compiler->addExtension('totp', new TotpAuthenticatorExtension());
	$compiler->addConfig([
		'totp' => [
			'timeWindow' => 2,
			'issuer' => 'jiripudil.cz',
		],
	]);
});

$container = new $containerClassName();

\assert($container instanceof \Nette\DI\Container);
$totp = $container->getByType(TotpAuthenticator::class, false);
Assert::notNull($totp);

Assert::match(
	'~^otpauth://totp/jiripudil\.cz:jiripudil\?secret=[A-Z2-7]{32}&issuer=jiripudil\.cz$~',
	$totp->getTotpUri($totp->getRandomSecret(), 'jiripudil')
);
