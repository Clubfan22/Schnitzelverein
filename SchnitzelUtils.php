<?php
include_once 'SchnitzelDB.php';
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
	
	static function keepSessionAlive($token, $stay = 0){
		include 'Settings.php';
		$db = new SchnitzelDB();
		$db->connect();
		$session['token'] = $token;
		if ($stay == 1){
			$endDate = time() + 365 * 24 * 60 * 60;
		} else {
			$endDate = time() + $sessionDuration * 60;
		}
		$session['end_date'] = $endDate;
		$db->updateSession($session);		
		setcookie("token", $token, $endDate);
		setcookie("stay", $stay, $endDate);
	}
	static function displayEvents(array $events, $mode = 'html', $isLoggedIn = false) {
		switch ($mode) {
			case 'html':
				foreach ($events as $event) {
					$date = new DateTime($event["event_date"]);
					setlocale(LC_TIME, "de_DE.utf8");
					$event["date"] = strftime("%e. %B %Y", (int)$date->format("U"));
					$event["time"] = $date->format("H:i");
					echo "<div class=\"row\">";
						echo "<div class=\"col-lg-12\">";
							echo "<div class=\"termin\" id=\"event-".$event["id"]."\">";
								echo "<div class=\"col-lg-12 termin-date\">";
									echo "<h3>";
									echo $event["date"];
									if($isLoggedIn){
										echo "<button type=\"submit\" name=\"delete\" value=\"".$event["id"]."\" class=\"btn btn-primary\" id=\"removeBtn".$event["id"]."\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></button>";
										echo "<a href=\"index.php?page=edittermin&id=".$event["id"]."\" class=\"btn btn-primary\" id=\"editBtn".$event["id"]."\" role=\"button\"\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a>";
									}
									echo "</h3>";									
								echo "</div>";
								echo "<div class=\"termin-heading col-md-6 col-sm-12\">";
									echo "<h4>" . $event["time"] . " Uhr</h4>";
									echo "<address>";
										echo "<strong>" . $event["location"] . "</strong><br>";
										echo $event["street"] . "<br>";
										echo $event["city"] . "<br>";
									echo "</address>";
								echo "</div>";
								echo "<div class=\"termin-body col-md-6 col-sm-12\">";
									echo $event["text"];
								echo "</div>";
								echo "<div style=\"display: block; clear: both;\"></div>";
							echo "</div>";
						echo "</div>";
					echo "</div>";
				}
				break;
		}
	}

}
