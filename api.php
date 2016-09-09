<?php

$action = strtolower(filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING));
if ($action == 'login') {
	$username = strtolower(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
	$password = strtolower(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
	$db = new SchnitzelDB();
	$db->connect();
	$user = $db->selectUserByName($username);
	$saltedHash = SchnitzelUtils::hashPassword($password, $user['salt']);
	if ($saltedHash == $user['password']) {
		include("./Settings.php");
		$session = array();
		$token = SchnitzelUtils::getToken();
		$session['token'] = $token;
		$endDate = microtime() + $sessionDuration * 60 * 1000;
		$session['end_date'] = $endDate;
		$session['user_id'] = null;
		$db->createSession($session);
		setcookie("token", $token, $endDate);
		$response = array();
		$response["login"] = "successfull";
		$response['token'] = $token;
		echo json_encode($response);
	}
} else {
	$token = strtolower(filter_input(INPUT_COOKIE, 'token', FILTER_SANITIZE_STRING));
	
		switch ($module) {
			case 'events':
				switch ($action) {
					case 'add':
						if (SchnitzelUtils::isLoggedIn($token)) {
							$event = array();
							$date = new DateTime(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING), new DateTimeZone("Europe/Berlin"));
							$event['date'] = $date->format('U');
							$event['location'] = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
							$event['street'] = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_STRING);
							$event['city'] = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
							$event['text'] = filter_input(INPUT_POST, 'text', FILTER_UNSAFE_RAW);
							$db = new SchnitzelDB();
							$db->connect();
							$response = array();
							$res = $db->createEvent($event);
							if ($res == false){
								$response['add'] = "failed";
							} else {
								$response['add'] = $res;
							}
							echo json_encode($response);
						}
						break;
					case 'list':
						$order = filter_input(INPUT_POST, 'order', FILTER_SANITIZE_STRING);
						$limit = filter_input(INPUT_POST, 'limit', FILTER_SANITIZE_NUMBER_INT);
						$db = new SchnitzelDB();
						$db -> connect();
						$events = $db->listEventsByTime($order, $limit);
						echo json_encode($events);
						break;
					case 'delete':
						if (SchnitzelUtils::isLoggedIn($token)) {
							
						}
						break;
					case 'edit':
						if (SchnitzelUtils::isLoggedIn($token)) {
							
						}
						break;
					case 'show':
						break;
				}
				break;
			case 'users':
				switch ($action) {
					case 'add':
						if (SchnitzelUtils::isLoggedIn($token)) {
							
						}
						break;
					case 'list':
						if (SchnitzelUtils::isLoggedIn($token)) {
							
						}
						break;
					case 'delete':
						if (SchnitzelUtils::isLoggedIn($token)) {
							
						}
						break;
					case 'edit':
						if (SchnitzelUtils::isLoggedIn($token)) {
							
						}
						break;
					case 'show':
						if (SchnitzelUtils::isLoggedIn($token)) {
							
						}
						break;
				}
		}
	
}
?>
