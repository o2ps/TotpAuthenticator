<?php

use Oops\TotpAuthenticator\Security\TotpAuthenticator;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
$timeProvider = Mockery::mock('Oops\TotpAuthenticator\Utils\TimeProvider')
	->shouldReceive('getMicroTime')->andReturn(1415778000)->getMock();

$googleAuthenticator = (new TotpAuthenticator($timeProvider))->setTimeWindow(0);
Assert::false($googleAuthenticator->verifyCode('209170', $seed)); // offset = -2
Assert::false($googleAuthenticator->verifyCode('101895', $seed)); // offset = -1
Assert::true($googleAuthenticator->verifyCode('292224', $seed)); // offset = 0
Assert::false($googleAuthenticator->verifyCode('800413', $seed)); // offset = +1
Assert::false($googleAuthenticator->verifyCode('223013', $seed)); // offset = +2

$googleAuthenticator = (new TotpAuthenticator($timeProvider))->setTimeWindow(1);
Assert::false($googleAuthenticator->verifyCode('209170', $seed)); // offset = -2
Assert::true($googleAuthenticator->verifyCode('101895', $seed)); // offset = -1
Assert::true($googleAuthenticator->verifyCode('292224', $seed)); // offset = 0
Assert::true($googleAuthenticator->verifyCode('800413', $seed)); // offset = +1
Assert::false($googleAuthenticator->verifyCode('223013', $seed)); // offset = +2

$googleAuthenticator = (new TotpAuthenticator($timeProvider))->setTimeWindow(2);
Assert::true($googleAuthenticator->verifyCode('209170', $seed)); // offset = -2
Assert::true($googleAuthenticator->verifyCode('101895', $seed)); // offset = -1
Assert::true($googleAuthenticator->verifyCode('292224', $seed)); // offset = 0
Assert::true($googleAuthenticator->verifyCode('800413', $seed)); // offset = +1
Assert::true($googleAuthenticator->verifyCode('223013', $seed)); // offset = +2

Mockery::close();
