<link href="css/login.css" rel="stylesheet">
<div class="container" id="main-content">
	<div class="col-md-12">
		<div class="row">
			<?php
			global $isLoggedIn;
			if ($isLoggedIn) {
				echo "<p>Sie sind bereits eingeloggt. <a href=\"index.php?page=logout\" target=\"_blank\">Abmelden</a></p>";
			} else {
				echo "<form action=\"index.php?page=login\" method=\"post\" class=\"form-horizontal\">";
				echo "<div class=\"col-md-10 col-md-offset-1\">";				
				echo "<div class=\"jumbotron\">";
				echo "<h1>Anmelden</h1>";
				if (isset($_POST["sent"])) {
					$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
					$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
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
						$session['user_id'] = $user['id'];
						$db->createSession($session);
						setcookie("token", $token, $endDate);
						echo "<script type=\"text/javascript\">window.location.href = \"http://ammon.diskstation.me/Schnitzelverein/index.php\";</script>";
					} else {
						echo "<p>Anmelden fehlgeschlagen! Benutzername und/oder Passwort falsch!</p>";
					}
				}
				echo "<div class=\"form-group\">";
				echo "<label for=\"inputUsername\" class=\"col-sm-2 control-label\">Benutzername:</label>";
				echo "<div class=\"col-sm-10 col-md-6\">";
				echo "<input type=\"text\" class=\"form-control\" id=\"inputUsername\" placeholder=\"Benutzername\" name=\"username\">";
				echo "</div>";
				echo "</div>";
				echo "<div class = \"form-group\">";
				echo "<label for = \"inputPassword\" class = \"col-sm-2 control-label\">Passwort:</label>";
				echo "<div class = \"col-sm-10 col-md-6\">";
				echo "<input type = \"password\" class = \"form-control\" id = \"inputPassword\" placeholder = \"Passwort\"  name=\"password\">";
				echo "</div>";
				echo "</div>";
				echo "<button type=\"submit\" class=\"btn btn-primary\" name=\"sent\">Anmelden</button>";
				echo "</div>";
				echo "</div>";
				echo"</form>";
			}
			?>
		</div>
	</div>
</div>
