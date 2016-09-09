<?php
include 'SchnitzelDB.php';
include 'SchnitzelUtils.php';

$action = strtolower(filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING));
if ($action == 'login') {
	$username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_GET, 'password', FILTER_SANITIZE_STRING);
	$db = new SchnitzelDB();
	$db->connect();
	$user = $db->selectUserByName($username);
	$saltedHash = SchnitzelUtils::hashPassword($password, $user['salt']);
	if ($saltedHash == $user['password']) {
		include 'Settings.php';
		$session = array();
		$token = SchnitzelUtils::getToken(128);
		$session['token'] = $token;
		$endDate = time() + $sessionDuration * 60;
		$session['end_date'] = $endDate;
		//echo microtime();
		//echo $user['id'];
		$session['user_id'] = $user['id'];
		$db->createSession($session);
		setcookie("token", $token, $endDate);
		$response = array();
		$response["login"] = "successfull";
		$response['token'] = $token;
		echo json_encode($response);
	} else {
		$response = ["login" => "failed"];
		echo json_encode($response);
	}
} else {
	$module = strtolower(filter_input(INPUT_GET, 'module', FILTER_SANITIZE_STRING));
	$token = filter_input(INPUT_COOKIE, 'token', FILTER_UNSAFE_RAW);	
		switch ($module) {
			case 'events':
				switch ($action) {
					case 'add':
						if (SchnitzelUtils::isLoggedIn($token)) {
							$event = array(); 
							$date = new DateTime(filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING), new DateTimeZone("Europe/Berlin"));
							$event['date'] =$date->format(DATE_ATOM);
							$event['location'] = filter_input(INPUT_GET, 'location', FILTER_SANITIZE_STRING);
							$event['street'] = filter_input(INPUT_GET, 'street', FILTER_SANITIZE_STRING);
							$event['city'] = filter_input(INPUT_GET, 'city', FILTER_SANITIZE_STRING);
							$event['text'] = filter_input(INPUT_GET, 'text', FILTER_UNSAFE_RAW);
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
						$order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING);
						$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT);
						$db = new SchnitzelDB();
						$db->connect();
						$events = $db->listEventsByTime($order, $limit);
						echo json_encode($events);
						break;
					case 'delete':
						if (SchnitzelUtils::isLoggedIn($token)) {
							$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
							$response = array();
							if ($id > 0){
								$db = new SchnitzelDB();
								$db->connect();
								$res = $db->deleteEvent($id);
								echo $res;
								if ($res == false){
									$response['delete'] = "failed";
								} else {
									$response['delete'] = "successfull";
								}								
							} else {
								$response['delete'] = "failed";
							}
							echo json_encode($response);							
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
