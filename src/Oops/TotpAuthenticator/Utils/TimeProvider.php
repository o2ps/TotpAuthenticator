<?php

namespace Oops\TotpAuthenticator\Utils;


class TimeProvider implements ITimeProvider
{
	/**
	 * @return float
	 */
	public function getMicroTime()
	{
		return microtime(TRUE);
	}
}
