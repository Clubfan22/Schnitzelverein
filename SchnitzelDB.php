<?php

/*
 */

/**
 * Description of SchnitzelDB
 *
 * @author Marco Ammon (Clubfan)
 */
class SchnitzelDB {

	private $mysqli;

	function connect() {
		include 'Settings.php';
		$mysqli = new mysqli("localhost", $dbUser, $dbPassword, $dbName);
		$mysqli->set_charset('utf8');
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			return false;
		}
		$this->mysqli = $mysqli;
	}

	function createTables() {
		$mysqli = $this->mysqli;
		$tables = ["users", "events", "sessions"];
		foreach ($tables as $table) {
			$sql = file_get_contents("./sql/create_" . $table . ".sql");
			if (!$mysqli->query($sql)) {
				echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
				return false;
			}
		}
	}

	function createUser(array $user) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("INSERT INTO users(id, username, password, salt, email) VALUES (null, ?, ?, ?, ?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("ssss", $user['username'], $user['password'], $user['salt'], $user['email'])) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
	}

	function updateUser(array $user) {
		$newPassword = (isset($user["password"])||isset($user["salt"]));
		$sql = "UPDATE users SET username = ?, ";
		if ($newPassword){
			$sql .= "password = ?, salt = ?, ";
		}
		$sql .= "email = ? WHERE id=?";
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare($sql))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if ($newPassword){
			if (!$stmt->bind_param("ssssi", $user['username'], $user['password'], $user['salt'], $user['email'], $user['id'])) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				return false;
			}
		} else {
			if (!$stmt->bind_param("ssi", $user['username'], $user['email'], $user['id'])) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				return false;
			}
		}
		
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		return true;
	}

	function deleteUser($id) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("DELETE FROM users WHERE id=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("i", $id)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
	}
	
	function selectUserByID($id) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("SELECT * FROM users WHERE id=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("i", $id)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		$res = $stmt->get_result();
		$user = $res->fetch_assoc();
		return $user;
	}

	function selectUserByName($username) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("SELECT * FROM users WHERE username=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("s", $username)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		$res = $stmt->get_result();
		$user = $res->fetch_assoc();
		return $user;
	}
	
	function listUsers($order="ASC"){
		if (!($order == 'DESC' || $order == 'ASC')) {
			return false;
		}
		$mysqli = $this->mysqli;
		$sql = "SELECT id, username, email FROM users ORDER BY id " . $order;
		
		if (!($stmt = $mysqli->prepare($sql))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		$res = $stmt->get_result();
		return $res->fetch_all(MYSQLI_ASSOC);
	}

	function createSession(array $session) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("INSERT INTO sessions(token, end_date, user_id) VALUES (?, FROM_UNIXTIME(?), ?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("sis", $session['token'], $session['end_date'], $session['user_id'])) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
	}

	function updateSession(array $session) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("UPDATE sessions SET end_date = FROM_UNIXTIME(?) WHERE token=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("is", $session['end_date'], $session['token'])) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
	}

	function deleteSession($token) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("DELETE FROM sessions WHERE token=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("s", $token)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
	}

	function selectSessionByToken($token) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("SELECT * FROM sessions WHERE token = ?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("s", $token)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		$res = $stmt->get_result();
		$session = $res->fetch_assoc();
		return $session;
	}

	function createEvent(array $event) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("INSERT INTO events(id, event_date, location, street, city, text) VALUES (null, ?, ?, ?, ?, ?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("sssss", $event['event_date'], $event['location'], $event['street'], $event['city'], $event['text'])) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		return $mysqli->insert_id;
	}

	function updateEvent(array $event) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("UPDATE events SET event_date = ?, location = ?, street = ?, city = ?, text = ? WHERE id=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("sssssi", $event['event_date'], $event['location'], $event['street'], $event['city'], $event['text'], $event['id'])) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		return $event["id"];
	}

	function deleteEvent($id) {
		$mysqli = $this->mysqli;
		if (!($stmt = $mysqli->prepare("DELETE FROM events WHERE id=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("i", $id)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		return true;
	}

	function listEventsByTime($order = 'DESC', $limit = 0) {
		if (!($order == 'DESC' || $order == 'ASC' || $oder == '')) {
			return false;
		}
		$mysqli = $this->mysqli;
		$sql = "SELECT * FROM events ORDER BY event_date " . $order;
		if (is_numeric($order)) {
			if ($order > 0) {
				$sql .= " LIMIT " . $limit;
			}
		}
		if (!($stmt = $mysqli->prepare($sql))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		$res = $stmt->get_result();
		return $res->fetch_all(MYSQLI_ASSOC);
	}

	function selectEventByID($id) {
		$mysqli = $this->mysqli;
		$sql = "SELECT * FROM events WHERE id = ?";
		if (!($stmt = $mysqli->prepare($sql))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return false;
		}
		if (!$stmt->bind_param("i", $id)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			return false;
		}
		$res = $stmt->get_result();
		return $res->fetch_assoc();
	}

}
