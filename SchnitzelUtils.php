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
	static function isAdministrator($token) {
		$db = new SchnitzelDB();
		$db->connect();
		$user = $db->selectUserBySessionToken($token);
		return $user["administrator"];
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
	static function displayEvents(array $events, $mode = 'html', $isAdministrator = false) {
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
									echo "<div class=\"btn-group\">";
									echo "<button type=\"button\" class=\"btn btn-primary dropdown-toggle share-button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
										echo "<i class=\"fa fa-share-alt\" aria-hidden=\"true\"></i>";
									echo "</button>";
									echo "<ul class=\"dropdown-menu dropdown-menu-right\">";
									$title = urlencode("Termin des 1. Deutschen Schnitzelvereins am ".$event["date"]);
									$url = urlencode("https://ammon.diskstation.me/Schnitzelverein/index.php?page=termine#event-".$event["id"]);
										echo "<li><a target=\"_blank\" href=\"http://www.facebook.com/sharer.php?s=100&p[title]=".$title."&p[url]=".$url."\">";
										echo "<i class=\"fa fa-facebook\" aria-hidden=\"true\"></i>&nbsp;Facebook</a></li>";
										echo "<li><a target=\"_blank\" href=\"http://twitter.com/share?text=".$title."&url=".$url."&counturl=".$url. "\">";
										echo "<i class=\"fa fa-twitter\" aria-hidden=\"true\"></i>&nbsp;Twitter</a></li>";
										echo "<li class=\"hidden-lg hidden-md\"><a target=\"_blank\" href=\"whatsapp://send?text=".$title.": ".$url."\">";
										echo "<i class=\"fa fa-whatsapp\" aria-hidden=\"true\"></i>&nbsp;WhatsApp</a></li>";									
									echo "</ul>";
									echo "</div>";
									if($isAdministrator){
										echo "<button type=\"submit\" name=\"delete\" value=\"".$event["id"]."\" class=\"btn btn-primary\" id=\"removeBtn".$event["id"]."\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></button>";
										echo "<a href=\"index.php?page=edittermin&id=".$event["id"]."\" class=\"btn btn-primary\" id=\"editBtn".$event["id"]."\" role=\"button\"\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a>";
									}
									echo "</h3>";									
								echo "</div>";
								echo "<div class=\"termin-heading col-md-6 col-sm-12\">";
									echo "<h4>" . $event["time"] . " Uhr</h4>";
									echo "<address>";
										echo "<strong>" . $event["location"] . "<a href=\"https://www.google.de/maps?q=".urlencode($event["location"]." ".$event["street"]." ".$event["city"])."\"><i class=\"fa fa-map-marker location-marker\" aria-hidden=\"true\"></i></a></strong><br>";
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
