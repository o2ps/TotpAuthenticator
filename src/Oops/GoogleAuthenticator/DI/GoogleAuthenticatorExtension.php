<?php

namespace Oops\GoogleAuthenticator\DI;

use Nette\DI\CompilerExtension;


class GoogleAuthenticatorExtension extends CompilerExtension
{

	private $defaults = [
		'timeWindow' => 1,
		'issuer' => NULL,
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$builder->addDefinition($this->prefix('timeProvider'))
			->setClass('Oops\GoogleAuthenticator\Utils\TimeProvider')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('authenticator'))
			->setClass('Oops\GoogleAuthenticator\Security\GoogleAuthenticator', [$this->prefix('@timeProvider')])
			->addSetup('setTimeWindow', [$config['timeWindow']])
			->addSetup('setIssuer', [$config['issuer']]);
	}

}
