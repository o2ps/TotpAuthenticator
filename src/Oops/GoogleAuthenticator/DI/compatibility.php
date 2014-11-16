<?php

if ( ! defined('OOPS_NETTE_DI') && ! class_exists('Nette\DI\CompilerExtension')) {
	$aliases = <<<GEN
namespace Nette\DI {
	class CompilerExtension {}
}
GEN;
	eval($aliases);
	define('OOPS_NETTE_DI', 1);
}
