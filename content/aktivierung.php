<link href="css/registration.css" rel="stylesheet">
<div class="container" id="main-content">
	<div class="col-md-12">
		<div class="row">
			<?php
			global $isLoggedIn;
			if ($isLoggedIn) {
				echo "<p>Sie sind bereits eingeloggt. <a href=\"index.php?page=logout\" target=\"_blank\">Abmelden</a></p>";
			} else {
				$activateToken = filter_input(INPUT_GET, "token", FILTER_UNSAFE_RAW);
				echo "<form action=\"index.php?page=aktivierung&token=".$activateToken."\" method=\"post\" class=\"form-horizontal\">";
				echo "<div class=\"col-md-10 col-md-offset-1\">";
				echo "<div class=\"jumbotron\">";
				echo "<h1>Aktivieren</h1>";
				$db = new SchnitzelDB();
				$db->connect();
				if (isset($_GET["token"])) {
					$activateUser = $db->selectUserByPasswordHash($activateToken);
					if ($activateUser != false && $activateUser != null) {
						if (isset($_POST["password"])) {
							$password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
							$activateUser["salt"] = SchnitzelUtils::getToken(63);
							$activateUser["password"] = SchnitzelUtils::hashPassword($password, $activateUser["salt"]);
							$res = $db->updateUser($activateUser);
							if ($res != false) {
								echo "<p>Benutzer wurde aktiviert, Anmelden mit gerade vergebenem Passwort möglich!</p>";
							} else {
								echo "<p>Beim Aktivieren des Benutzers ist ein Fehler aufgetreten.</p>";
							}
						} else {
							echo "<div class=\"form-group\">";
							echo "<label for=\"inputPassword\" class=\"col-sm-2 control-label\">Neues Passwort:</label>";
							echo "<div class=\"col-sm-10 col-md-6\">";
							echo "<input type=\"password\" class=\"form-control\" id=\"inputPassword\" placeholder=\"Neues Passwort\" name=\"password\">";
							echo "</div>";
							echo "</div>";
							echo "<button type=\"submit\" class=\"btn btn-primary\" name=\"sent\">Aktivieren</button>";
						}
					} else {
						echo "<p>Es konnte kein Benutzer zum angegebenen Token gefunden werden.</p>";
					}					
				} else {
					echo "<p>Es wurde kein Token übergeben, deswegen kann kein Benutzer aktiviert werden.</p>";
				}
				echo "</div>";
				echo "</div>";
				echo "</form>";
			}
			?>
		</div>
	</div>
</div>