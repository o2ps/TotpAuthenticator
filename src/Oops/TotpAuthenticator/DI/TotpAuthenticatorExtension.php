<?php

namespace Oops\TotpAuthenticator\DI;


use Nette\DI\CompilerExtension;
use Nette\Utils\Validators;
use Oops\TotpAuthenticator\Security\TotpAuthenticator;
use Oops\TotpAuthenticator\Utils\TimeProvider;


class TotpAuthenticatorExtension extends CompilerExtension
{

    private $defaults = [
        'timeWindow' => 1,
        'issuer' => '',
    ];



    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        Validators::assertField($config, 'timeWindow', 'int');
        Validators::assertField($config, 'issuer', 'string:1..');

        $builder->addDefinition($this->prefix('timeProvider'))
            ->setFactory(TimeProvider::class)
            ->setAutowired(FALSE);

        $builder->addDefinition($this->prefix('authenticator'))
            ->setFactory(TotpAuthenticator::class, [$this->prefix('@timeProvider')])
            ->addSetup('setTimeWindow', [$config['timeWindow']])
            ->addSetup('setIssuer', [$config['issuer']]);
    }

}
