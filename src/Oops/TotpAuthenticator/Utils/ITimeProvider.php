<?php

namespace Oops\TotpAuthenticator\Utils;


interface ITimeProvider
{
	/**
	 * @return float
	 */
	function getMicroTime();
}
