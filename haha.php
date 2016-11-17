<?php

class Dragonball
{
	protected $ballcount ;
	public function __construct()
	{
		$this->ballcount = 0;
	}

	public function iFoundABall()
	{
		if ($this->ballcount == 7) {
			$this->AskWish();
		}

		$this->ballcount ++;
	}

	public function AskWish()
	{
		$this->ballcount = 0;

		echo "You can now ask your wish";
	}
}

$punyMan = new Dragonball;

$punyMan->iFoundABall();
$punyMan->iFoundABall();
$punyMan->iFoundABall();
$punyMan->iFoundABall();
$punyMan->iFoundABall();
$punyMan->iFoundABall();
$punyMan->iFoundABall();
$punyMan->iFoundABall();
$punyMan->iFoundABall();




?>
