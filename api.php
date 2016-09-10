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
						$event['date'] = $date->format(DATE_ATOM);
						$event['location'] = filter_input(INPUT_GET, 'location', FILTER_SANITIZE_STRING);
						$event['street'] = filter_input(INPUT_GET, 'street', FILTER_SANITIZE_STRING);
						$event['city'] = filter_input(INPUT_GET, 'city', FILTER_SANITIZE_STRING);
						$event['text'] = filter_input(INPUT_GET, 'text', FILTER_UNSAFE_RAW);
						$db = new SchnitzelDB();
						$db->connect();
						$response = array();
						$res = $db->createEvent($event);
						if ($res == false) {
							$response['add'] = "failed";
						} else {
							$response['add'] = $res;
						}
						echo json_encode($response);
						SchnitzelUtils::keepSessionAlive($token);
					} else {
						$response = ['add' => 'failed', 'login' => false];
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
						if ($id > 0) {
							$db = new SchnitzelDB();
							$db->connect();
							$res = $db->deleteEvent($id);
							echo $res;
							if ($res == false) {
								$response['delete'] = "failed";
							} else {
								$response['delete'] = "successfull";
							}
						} else {
							$response['delete'] = "failed";
						}
						echo json_encode($response);
						SchnitzelUtils::keepSessionAlive($token);
					}
					break;
				case 'edit':
					if (SchnitzelUtils::isLoggedIn($token)) {
						$event = array();
						$event['event_date'] = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
						$date = new DateTime(filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING), new DateTimeZone("Europe/Berlin"));
						$event['date'] = $date->format(DATE_ATOM);
						$event['location'] = filter_input(INPUT_GET, 'location', FILTER_SANITIZE_STRING);
						$event['street'] = filter_input(INPUT_GET, 'street', FILTER_SANITIZE_STRING);
						$event['city'] = filter_input(INPUT_GET, 'city', FILTER_SANITIZE_STRING);
						$event['text'] = filter_input(INPUT_GET, 'text', FILTER_UNSAFE_RAW);
						$db = new SchnitzelDB();
						$db->connect();
						$response = array();
						$res = $db->updateEvent($event);
						if ($res == false) {
							$response['edit'] = "failed";
						} else {
							$response['edit'] = $res;
						}
						echo json_encode($response);
						SchnitzelUtils::keepSessionAlive($token);
					} else {
						$response = ['edit' => 'failed', 'login' => false];
						echo json_encode($response);
					}
					break;
				case 'show':
					$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
					$db = new SchnitzelDB();
					$db->connect();
					$event = $db->selectEventByID($id);
					echo json_encode($event);
					break;
			}
			break;
		case 'users':
			switch ($action) {
				case 'add':
					if (SchnitzelUtils::isLoggedIn($token)) {
						$user = array();
						$user['username'] = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
						$password = filter_input(INPUT_GET, 'password', FILTER_SANITIZE_STRING);
						$salt = SchnitzelUtils::getToken(63);
						$user['salt'] = $salt;
						$user['password'] = SchnitzelUtils::hashPassword($password, $salt);
						$user['email'] = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);
						$db = new SchnitzelDB();
						$db->connect();
						$response = array();
						$res = $db->createUser($user);
						if ($res == false) {
							$response['add'] = "failed";
						} else {
							$response['add'] = $res;
						}
						echo json_encode($response);
						SchnitzelUtils::keepSessionAlive($token);
					} else {
						$response = ['add' => 'failed', 'login' => false];
						echo json_encode($response);
					}
					break;
				case 'list':
					if (SchnitzelUtils::isLoggedIn($token)) {
						$order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING);
						$db = new SchnitzelDB();
						$db->connect();
						$users = $db->listUsers($order);
						echo json_encode($users);
						SchnitzelUtils::keepSessionAlive($token);
					} else {
						$response = ['list' => 'failed', 'login' => false];
						echo json_encode($response);
					}
					break;
				case 'delete':
					if (SchnitzelUtils::isLoggedIn($token)) {
						$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
						$response = array();
						if ($id > 0) {
							$db = new SchnitzelDB();
							$db->connect();
							$res = $db->deleteUser($id);
							echo $res;
							if ($res == false) {
								$response['delete'] = "failed";
							} else {
								$response['delete'] = "successfull";
							}
						} else {
							$response['delete'] = "failed";
						}
						echo json_encode($response);
						SchnitzelUtils::keepSessionAlive($token);
					} else {
						$response = ['delete' => 'failed', 'login' => false];
						echo json_encode($response);
					}
					break;
				case 'edit':
					if (SchnitzelUtils::isLoggedIn($token)) {
						$user = array();
						$user['id'] = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
						$user['username'] = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
						$password = filter_input(INPUT_GET, 'password', FILTER_SANITIZE_STRING);
						$salt = SchnitzelUtils::getToken(63);
						$user['salt'] = $salt;
						$user['password'] = SchnitzelUtils::hashPassword($password, $salt);
						$user['email'] = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);
						$db = new SchnitzelDB();
						$db->connect();
						$response = array();
						$res = $db->updateUser($user);
						if ($res == false) {
							$response['edit'] = "failed";
						} else {
							$response['edit'] = $res;
						}
						echo json_encode($response);
						SchnitzelUtils::keepSessionAlive($token);
					} else {
						$response = ['edit' => 'failed', 'login' => false];
						echo json_encode($response);
					}
					break;
				case 'show':
					if (SchnitzelUtils::isLoggedIn($token)) {
						$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
						$db = new SchnitzelDB();
						$db->connect();
						$user = $db->selectUserByID($id);
						echo json_encode($user);
						SchnitzelUtils::keepSessionAlive($token);
						break;
					} else {
						$response = ['list' => 'failed', 'login' => false];
						echo json_encode($response);
					}
					break;
			}
	}
}
?>
