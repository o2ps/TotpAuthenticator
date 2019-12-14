<?php declare(strict_types = 1);

namespace Oops\TotpAuthenticator\Utils;


/**
 * @internal
 */
class TimeProvider
{

	public function getMicroTime(): float
	{
		return microtime(TRUE);
	}

}
