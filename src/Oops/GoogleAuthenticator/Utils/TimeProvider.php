<?php

namespace Oops\GoogleAuthenticator\Utils;


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
