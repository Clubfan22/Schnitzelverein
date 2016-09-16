<link href="css/registration.css" rel="stylesheet">
<div class="container" id="main-content">
	<div class="col-md-12">
		<div class="row">
			<?php
			global $isLoggedIn;
			if ($isLoggedIn) {
				echo "<p>Sie sind bereits eingeloggt. <a href=\"index.php?page=logout\" target=\"_blank\">Abmelden</a></p>";
			} else {
				echo "<form action=\"index.php?page=registration\" method=\"post\" class=\"form-horizontal\">";
				echo "<div class=\"col-md-10 col-md-offset-1\">";				
				echo "<div class=\"jumbotron\">";
				echo "<h1>Anmelden</h1>";
				if (isset($_POST["sent"])) {
					$newUser = array();
					$newUser["username"] = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
					$newUser["email"] = $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
					$newUser["salt"] = SchnitzelUtils::getToken(63);
					$newUser["password"] = SchnitzelUtils::hashPassword(SchnitzelUtils::getToken(63), $newUser["salt"]);
					$newUser["administrator"] = 0;
					$db = new SchnitzelDB();
					$db->connect();					
					$res = $db->createUser($newUser);
					if ($res!=false){
						//TODO: Email versenden mit Link zur Aktivierung (?token=$newUser["password"] &aktivierung=true)
						echo "<p>Benutzer wurde erstellt! Es wurde eine Email zur Aktivierung versendet.</p>";
					} else {
						echo "<p>Benutzer konnte nicht erstellt werden.</p>";
					}
				} else {
					
					echo "<div class=\"form-group\">";
					echo "<label for=\"inputUsername\" class=\"col-sm-2 control-label\">Benutzername:</label>";
					echo "<div class=\"col-sm-10 col-md-6\">";
					echo "<input type=\"text\" class=\"form-control\" id=\"inputUsername\" placeholder=\"Benutzername\" name=\"username\">";
					echo "</div>";
					echo "</div>";
					echo "<div class = \"form-group\">";
					echo "<label for = \"inputEmail\" class = \"col-sm-2 control-label\">Email:</label>";
					echo "<div class = \"col-sm-10 col-md-6\">";
					echo "<input type = \"text\" class = \"form-control\" id = \"inputEmail\" placeholder = \"Email\"  name=\"email\">";
					echo "</div>";
					echo "</div>";
					echo "<button type=\"submit\" class=\"btn btn-primary\" name=\"sent\">Registrieren</button>";
				}
				echo "</div>";
				echo "</div>";
				echo"</form>";
			}
			?>
		</div>
	</div>
</div>

