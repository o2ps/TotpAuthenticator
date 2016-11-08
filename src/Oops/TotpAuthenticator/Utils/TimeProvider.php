<?php

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
