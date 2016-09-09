<?php

/*
 */

/**
 * Description of SchnitzelUtils
 *
 * @author Marco Ammon (Clubfan)
 */
class SchnitzelUtils {

	static function hashPassword($password, $salt) {
		//TODO: Richtige Funktion schreiben
		$saltedPassword = $password . $salt;
		return $saltedPassword;
	}

	function crypto_rand_secure($min, $max) {
		$range = $max - $min;
		if ($range < 1)
			return $min; // not so random...
		$log = ceil(log($range, 2));
		$bytes = (int) ($log / 8) + 1; // length in bytes
		$bits = (int) $log + 1; // length in bits
		$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd >= $range);
		return $min + $rnd;
	}

	static function getToken($length) {
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet); // edited

		for ($i = 0; $i < $length; $i++) {
			$token .= $codeAlphabet[self::crypto_rand_secure(0, $max)];
		}

		return $token;
	}

	static function isLoggedIn($token) {
		$db = new SchnitzelDB();
		$db->connect();
		$session = $db->selectSessionByToken($token);
		echo time()."/n";
		echo $session['end_date'];
		$expiration = new DateTime($session['end_date']);
		//echo $expiration->format("U");
		return (time() < $expiration->format("U"));
	}

}
