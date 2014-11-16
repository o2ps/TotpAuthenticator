<?php

namespace Oops\GoogleAuthenticator\Utils;


interface ITimeProvider
{
	/**
	 * @return float
	 */
	function getMicroTime();
}
