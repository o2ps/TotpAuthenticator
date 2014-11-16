<?php

use Oops\GoogleAuthenticator\Security\GoogleAuthenticator;
use Oops\GoogleAuthenticator\Utils\ITimeProvider;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';

class TestTimeProvider implements ITimeProvider
{
	/** @var float */
	private $time;

	public function setMicroTime($time)
	{
		$this->time = $time;
		return $this;
	}

	public function getMicroTime()
	{
		return $this->time;
	}
}

$seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
$timeProvider = (new TestTimeProvider)->setMicroTime(1415778000);

$googleAuthenticator = (new GoogleAuthenticator($timeProvider))->setTimeWindow(0);
Assert::false($googleAuthenticator->verifyCode('209170', $seed)); // offset = -2
Assert::false($googleAuthenticator->verifyCode('101895', $seed)); // offset = -1
Assert::true($googleAuthenticator->verifyCode('292224', $seed)); // offset = 0
Assert::false($googleAuthenticator->verifyCode('800413', $seed)); // offset = +1
Assert::false($googleAuthenticator->verifyCode('223013', $seed)); // offset = +2

$googleAuthenticator = (new GoogleAuthenticator($timeProvider))->setTimeWindow(1);
Assert::false($googleAuthenticator->verifyCode('209170', $seed)); // offset = -2
Assert::true($googleAuthenticator->verifyCode('101895', $seed)); // offset = -1
Assert::true($googleAuthenticator->verifyCode('292224', $seed)); // offset = 0
Assert::true($googleAuthenticator->verifyCode('800413', $seed)); // offset = +1
Assert::false($googleAuthenticator->verifyCode('223013', $seed)); // offset = +2

$googleAuthenticator = (new GoogleAuthenticator($timeProvider))->setTimeWindow(2);
Assert::true($googleAuthenticator->verifyCode('209170', $seed)); // offset = -2
Assert::true($googleAuthenticator->verifyCode('101895', $seed)); // offset = -1
Assert::true($googleAuthenticator->verifyCode('292224', $seed)); // offset = 0
Assert::true($googleAuthenticator->verifyCode('800413', $seed)); // offset = +1
Assert::true($googleAuthenticator->verifyCode('223013', $seed)); // offset = +2
