<?php

namespace Oops\TotpAuthenticator\Security;

use Oops\TotpAuthenticator\InvalidArgumentException;
use Oops\TotpAuthenticator\Utils\TimeProvider;
use ParagonIE\ConstantTime\Base32;


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


	public function setTimeWindow(int $timeWindow): self
	{
		$this->timeWindow = $timeWindow;
		return $this;
	}


	public function setIssuer(string $issuer): self
	{
		$this->issuer = $issuer;
		return $this;
	}


	public function getTotpUri(string $secret, string $accountName): string
	{
		return "otpauth://totp/" . ($this->issuer !== NULL ? "{$this->issuer}:" : "") . "{$accountName}?secret={$secret}" . ($this->issuer !== NULL ? "&issuer={$this->issuer}" : "");
	}


	public function getRandomSecret(): string
	{
		return Base32::encodeUpper(random_bytes(20));
	}


	public function verifyCode($code, string $secret): bool
	{
		for ($offset = -$this->timeWindow; $offset <= $this->timeWindow; $offset++) {
			if ((int) $code === $this->getOneTimePassword($secret, $this->getTimestamp($offset))) {
				return TRUE;
			}
		}

		return FALSE;
	}


	private function getOneTimePassword(string $secret, string $timestamp): int
	{
		if ( ! preg_match('/^[A-Z2-7]+$/', $secret)) {
			throw new InvalidArgumentException("Seed contains invalid characters. Make sure it is a valid uppercase base32 string.");
		}

		if (strlen($secret) < 16) {
			throw new InvalidArgumentException("Seed is too short. It must be at least 16 base32 digits long.");
		}

		$hash = hash_hmac('sha1', $timestamp, Base32::decodeUpper($secret), TRUE);
		$offset = ord($hash[19]) & 0xF;

		return (
			((ord($hash[$offset+0]) & 0x7F) << 24) |
			((ord($hash[$offset+1]) & 0xFF) << 16) |
			((ord($hash[$offset+2]) & 0xFF) << 8) |
			((ord($hash[$offset+3]) & 0xFF))
		) % 1e6;
	}


	private function getTimestamp(int $offset): string
	{
		$timestamp = floor(($this->timeProvider->getMicroTime() + ($offset * 30)) / 30);
		return pack('N*', 0) . pack('N*', $timestamp);
	}

}
