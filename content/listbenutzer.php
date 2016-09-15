<link href="css/listbenutzer.css" rel="stylesheet">
<?php
global $isLoggedIn;
if (!$isLoggedIn) {
	die();
}
$db = new SchnitzelDB();
$db->connect();
$currentUser = $db->selectUserBySessionToken($token);
if (isset($_POST["create"])) {
	if ($isAdministrator) {
		$newUser = array();
		$newUser["username"] = filter_input(INPUT_POST, 'newUsername', FILTER_SANITIZE_STRING);
		$newUser["email"] = filter_input(INPUT_POST, 'newEmail', FILTER_SANITIZE_EMAIL);
		$password = filter_input(INPUT_POST, 'newPassword', FILTER_SANITIZE_STRING);
		$salt = SchnitzelUtils::getToken(63);
		$newUser['password'] = SchnitzelUtils::hashPassword($password, $salt);
		$newUser['salt'] = $salt;
		if (isset($_POST["newAdministrator"])){
			$admin = filter_input(INPUT_POST, 'newAdministrator', FILTER_SANITIZE_STRING);
			if ($admin=="on"){
				$newUser["administrator"] = 1;
			} else {
				$newUser["administrator"] = 0;
			}
		} else {
			$newUser['administrator'] = 0;
		}
		$res = $db->createUser($newUser);
		if ($res == false) {
			echo "<p>Beim Erstellen des Benutzers " . $newUser["username"] . " ist ein Fehler aufgetreten</p>";
		}
	} else {
		echo "<p>Du hast nicht genügend Benutzerrechte für die Erstellung neuer Benutzer.</p>";
	}
}
if (isset($_POST["update"])) {
	$newUser = array();
	$id = filter_input(INPUT_POST, 'update', FILTER_SANITIZE_NUMBER_INT);
	$store = true;
	if(!$isAdministrator){
		if ($currentUser['id']!=$id){
					echo "<p>Die ID des zu bearbeitenden Benutzers ist nicht deine. Nicht genügend Benutzerrrechte.</p>";
					$store = false;
		}
	}
	if ($store) {
		$newUser["id"] = $id;
		$newUser["username"] = filter_input(INPUT_POST, 'usernameInput' . $id, FILTER_SANITIZE_STRING);
		$newUser["email"] = filter_input(INPUT_POST, 'emailInput' . $id, FILTER_SANITIZE_EMAIL);
		$password = filter_input(INPUT_POST, 'passwordInput' . $id, FILTER_SANITIZE_STRING);
		if ($password != "") {
			$salt = SchnitzelUtils::getToken(63);
			$newUser["salt"] = $salt;
			$newUser["password"] = SchnitzelUtils::hashPassword($password, $salt);
		}
		if (isset($_POST["adminInput".$id])){
			$admin = filter_input(INPUT_POST, 'adminInput'.$id, FILTER_SANITIZE_STRING);
			if ($admin=="on"){
				$newUser["administrator"] = 1;
			} else {
				$newUser["administrator"] = 0;
			}
		} else {
			$newUser['administrator'] = 0;
		}
		$res = $db->updateUser($newUser);
		if ($res == false) {
			echo "<p>Beim Aktualisieren des Benutzers mit der ID " . $newUser["id"] . " (" . $newUser["username"] . ") ist ein Fehler aufgetreten</p>";
		}
	}
}
if (isset($_POST["delete"])) {
	$delete = true;
	$id = filter_input(INPUT_POST, 'delete', FILTER_SANITIZE_NUMBER_INT);		

	if(!$isAdministrator){
		if ($currentUser['id']!=$id){
			echo "<p>Die ID des zu löschenden Benutzers ist nicht deine. Nicht genügend Benutzerrrechte.</p>";
			$delete = false;
		}
	}
	if ($delete) {
		$res = $db->deleteUser($id);
		if ($res == false) {
			echo "<p>Beim Entfernen des Benutzers mit der ID " . $id . " ist ein Fehler aufgetreten</p>";
		}
	}
}
?>
<div class="container" id="main-content">
	<div class="col-md-12">
		<div class="row">
			<form action="index.php?page=listbenutzer" method="post">
				<div class="row">
					<div class="col-xs-12 col-sm-4 col-md-3">
						<label>Benutzername</label>							
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<label>Email-Adresse</label>							
					</div>
					<div class="col-xs-10 col-sm-8 col-md-3">
						<label>Neues Passwort</label>							
					</div>
					<div class="col-xs-2 col-sm-2 col-md-1">
						<label>Admin</label>							
					</div>
					<div class="col-xs-12 col-sm-2 col-md-1">
						<label>Aktionen</label>							
					</div>
				</div>
				<tbody>
					<?php
					if ($isAdministrator){
						$users = $db->listUsers("ASC");
					} else {
						$users[0] = $db->selectUserBySessionToken($token);
					}
					
					foreach ($users as $user) {
						echo "<div class=\"row benutzer-row\">";
						echo "<div class=\"col-xs-12 col-sm-4 col-md-3\"><input type=\"text\" class=\"form-control\" name=\"usernameInput" . $user["id"] . "\" id=\"usernameInput" . $user["id"] . "\" value=\"" . $user["username"] . "\"></div>";
						echo "<div class=\"col-xs-12 col-sm-6 col-md-4\"><input type=\"text\" class=\"form-control\" name=\"emailInput" . $user["id"] . "\" id=\"emailInput" . $user["id"] . "\" value=\"" . $user["email"] . "\"></div>";
						echo "<div class=\"col-xs-10 col-sm-8 col-md-3\"><input type=\"password\" class=\"form-control\" name=\"passwordInput" . $user["id"] . "\" id=\"passwordInput" . $user["id"] . "\" placeholder=\"Neues Passwort\"></div>";
						echo "<div class=\"col-xs-2 col-sm-2 col-md-1\"><input type=\"checkbox\" class=\"form-control\" name=\"adminInput" . $user["id"] . "\" id =\"adminInput" . $user["id"] . "\"";
						if($user["administrator"]==1){
							echo " checked";
						}
						if(!$isAdministrator){
							echo " disabled";
						}
						echo "></div>";
						echo "<div class=\"col-xs-12 col-sm-2 col-md-1 listbenutzer-buttons\">";
						echo "<button name=\"update\" value=\"" . $user["id"] . "\" type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-floppy-o\" aria-hidden=\"true\"></i></button>&nbsp;";
						echo "<button name=\"delete\" value=\"" . $user["id"] . "\" type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></button>";
						echo "</div>";
						echo "</div>";
					}
					if ($isAdministrator){
					echo "<div class=\"row\">";
						echo "<div class=\"col-xs-12 col-sm-4 col-md-3\">";
						echo "<input type=\"text\" name=\"newUsername\" class=\"form-control\" id=\"newUserUsername\" placeholder=\"Benutzername\">";					
					echo "</div>";
					echo "<div class=\"col-xs-12 col-sm-6 col-md-4\">";
						echo "<input type=\"text\" name=\"newEmail\" class=\"form-control\" id=\"newUserEmail\" placeholder=\"Email-Adresse\">";						
					echo "</div>";
					echo "<div class=\"col-xs-10 col-sm-8 col-md-3\">";
						echo "<input type=\"password\" name=\"newPassword\" class=\"form-control\" id=\"newUserPassword\" placeholder=\"Neues Passwort\">";							
					echo "</div>";
					echo "<div class=\"col-xs-2 col-sm-2 col-md-1\">";
						echo "<input type=\"checkbox\" name=\"newAdmin\" class=\"form-control\" id=\"newAdmin\">";							
					echo "</div>";
					echo "<div class=\"col-xs-12 col-sm-2 col-md-1\">";
						echo "<button type=\"submit\" name=\"create\" class=\"btn btn-primary\"><i class=\"fa fa-user-plus\" aria-hidden=\"true\"></i></button>";				
					echo "</div>";
					echo "</div>";
					}
					?>									
			</form>
		</div>
	</div>
</div>
