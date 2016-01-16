<?php

namespace Oops\TotpAuthenticator\Utils;


/**
 * @internal
 */
class TimeProvider
{

	/**
	 * @return float
	 */
	public function getMicroTime()
	{
		return microtime(TRUE);
	}

}
