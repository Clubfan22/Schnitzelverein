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
		return hash_pbkdf2("sha256", $password, $salt,256000,255);
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
		$expiration = new DateTime($session['end_date']);
		//echo $expiration->format("U");
		return (time() < $expiration->format("U"));
	}
	
	static function keepSessionAlive($token){
		include 'Settings.php';
		$db = new SchnitzelDB();
		$db->connect();
		$session['token'] = $token;
		$endDate = time() + $sessionDuration * 60;
		$session['end_date'] = $endDate;
		$db->updateSession($session);
		setcookie("token", $token, $endDate);
	}

}
