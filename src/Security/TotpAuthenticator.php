<?php declare(strict_types = 1);

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


	public function __construct(?TimeProvider $timeProvider = NULL)
	{
		$this->timeProvider = $timeProvider ?? new TimeProvider();
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
		$accountName = rawurlencode($accountName);
		$issuer = $this->issuer !== NULL ? rawurlencode($this->issuer) : NULL;
		return 'otpauth://totp/' . ($issuer !== NULL ? "$issuer:" : '') . "{$accountName}?secret={$secret}" . ($issuer !== NULL ? "&issuer=$issuer" : '');
	}


	public function getRandomSecret(int $length = 20): string
	{
		return Base32::encodeUpper(random_bytes($length));
	}


	/**
	 * @param string|int $code
	 */
	public function verifyCode($code, string $secret): bool
	{
		for ($offset = -$this->timeWindow; $offset <= $this->timeWindow; $offset++) {
			if (hash_equals((string) $code, $this->getOneTimePassword($secret, $offset))) {
				return TRUE;
			}
		}

		return FALSE;
	}


	private function getOneTimePassword(string $secret, int $offset): string
	{
		if ( ! preg_match('/^[A-Z2-7]+$/', $secret)) {
			throw new InvalidArgumentException("Secret contains invalid characters. Make sure it is a valid uppercase base32 string.");
		}

		if (strlen($secret) < 16) {
			throw new InvalidArgumentException("Secret is too short. It must be at least 128 bits long.");
		}

		$timestamp = $this->getTimestamp($offset);
		$secret = Base32::decodeUpper($secret);

		$hash = hash_hmac('sha1', $timestamp, $secret, TRUE);
		$offset = ord($hash[19]) & 0xF;

		$value = (
			((ord($hash[$offset+0]) & 0x7F) << 24) |
			((ord($hash[$offset+1]) & 0xFF) << 16) |
			((ord($hash[$offset+2]) & 0xFF) << 8) |
			((ord($hash[$offset+3]) & 0xFF))
		) % 1e6;

		return str_pad((string) $value, 6, '0', STR_PAD_LEFT);
	}


	private function getTimestamp(int $offset): string
	{
		$timestamp = floor(($this->timeProvider->getMicroTime() + ($offset * 30)) / 30);
		return pack('N*', 0) . pack('N*', $timestamp);
	}

}
