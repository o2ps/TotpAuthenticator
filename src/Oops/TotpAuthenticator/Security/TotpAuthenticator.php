<?php

namespace Oops\TotpAuthenticator\Security;

use Nette\Utils\Random;
use Oops\TotpAuthenticator\InvalidArgumentException;
use Oops\TotpAuthenticator\Utils\TimeProvider;


class TotpAuthenticator
{

	/** @var int */
	private $timeWindow = 1;

	/** @var string */
	private $issuer;

	/** @var TimeProvider */
	private $timeProvider;


	public function __construct(TimeProvider $timeProvider = NULL)
	{
		$this->timeProvider = $timeProvider ?: new TimeProvider();
	}


	/**
	 * @param int $timeWindow
	 * @return TotpAuthenticator provides fluent interface
	 */
	public function setTimeWindow($timeWindow)
	{
		$this->timeWindow = $timeWindow;
		return $this;
	}


	/**
	 * @param string $issuer
	 * @return TotpAuthenticator provides fluent interface
	 */
	public function setIssuer($issuer)
	{
		$this->issuer = $issuer;
		return $this;
	}


	/**
	 * @param string $secret
	 * @param string $accountName
	 * @return string
	 */
	public function getTotpUri($secret, $accountName)
	{
		return "otpauth://totp/" . ($this->issuer !== NULL ? "{$this->issuer}:" : "") . "{$accountName}?secret={$secret}" . ($this->issuer !== NULL ? "&issuer={$this->issuer}" : "");
	}


	/**
	 * @return string
	 */
	public function getRandomSecret()
	{
		return Random::generate(32, 'A-Z2-7');
	}


	/**
	 * @param string $code
	 * @param string $secret
	 * @return bool
	 */
	public function verifyCode($code, $secret)
	{
		for ($offset = -$this->timeWindow; $offset <= $this->timeWindow; $offset++) {
			if ((int) $code === (int) $this->getOneTimePassword($secret, $this->getTimestamp($offset))) {
				return TRUE;
			}
		}

		return FALSE;
	}


	/**
	 * @param string $secret
	 * @param int $timestamp
	 * @return int
	 */
	private function getOneTimePassword($secret, $timestamp)
	{
		if ( ! preg_match('/^[A-Z2-7]+$/i', $secret)) {
			throw new InvalidArgumentException("Seed contains invalid characters. Make sure it is a valid base32 string.");
		}

		if (strlen($secret) < 16) {
			throw new InvalidArgumentException("Seed is too short. It must be at least 16 base32 digits long.");
		}

		$hash = hash_hmac('sha1', $timestamp, $this->decodeBase32($secret), TRUE);
		$offset = ord($hash[19]) & 0xF;

		return (
			((ord($hash[$offset+0]) & 0x7F) << 24) |
			((ord($hash[$offset+1]) & 0xFF) << 16) |
			((ord($hash[$offset+2]) & 0xFF) << 8) |
			((ord($hash[$offset+3]) & 0xFF))
		) % pow(10, 6);
	}


	/**
	 * @param int $offset
	 * @return string
	 */
	private function getTimestamp($offset)
	{
		$timestamp = floor(($this->timeProvider->getMicroTime() + ($offset * 30)) / 30);
		return pack('N*', 0) . pack('N*', $timestamp);
	}


	/**
	 * @param string $base32
	 * @return string
	 */
	private function decodeBase32($base32)
	{
		$charMap = ["A" => 0, "B" => 1, "C" => 2, "D" => 3, "E" => 4, "F" => 5, "G" => 6, "H" => 7,
			"I" => 8, "J" => 9, "K" => 10, "L" => 11, "M" => 12, "N" => 13, "O" => 14, "P" => 15,
			"Q" => 16, "R" => 17, "S" => 18, "T" => 19, "U" => 20, "V" => 21, "W" => 22, "X" => 23,
			"Y" => 24, "Z" => 25, "2" => 26, "3" => 27, "4" => 28, "5" => 29, "6" => 30,"7" => 31
		];

		$base32 = strtoupper($base32);
		$length = strlen($base32);
		$n = $j = 0;
		$binary = '';

		for ($i = 0; $i < $length; $i++) {
			$n <<= 5;
			$n = $n + $charMap[$base32[$i]];
			$j += 5;

			if ($j >= 8) {
				$j -= 8;
				$binary .= chr(($n & (0xFF << $j)) >> $j);
			}
		}

		return $binary;
	}

}
