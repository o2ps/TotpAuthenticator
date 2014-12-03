<?php

namespace Oops\TotpAuthenticator\DI;

use Nette\DI\CompilerExtension;


class TotpAuthenticatorExtension extends CompilerExtension
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
			->setClass('Oops\TotpAuthenticator\Utils\TimeProvider')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('authenticator'))
			->setClass('Oops\TotpAuthenticator\Security\TotpAuthenticator', [$this->prefix('@timeProvider')])
			->addSetup('setTimeWindow', [$config['timeWindow']])
			->addSetup('setIssuer', [$config['issuer']]);
	}

}
