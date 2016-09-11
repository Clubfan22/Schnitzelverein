<link href="css/listbenutzer.css" rel="stylesheet">
<?php
global $isLoggedIn;
if (!isLoggedIn) {
	die();
}
if (isset($_POST["create"])) {
	//TODO: Passwort etc
}
if (isset($_POST["update"])) {
	$id = filter_input(INPUT_POST, 'update', FILTER_SANITIZE_NUMBER_INT);
	$username = filter_input(INPUT_POST, 'usernameInput' . $id, FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'emailInput' . $id, FILTER_SANITIZE_EMAIL);
	echo $id;
	echo $username;
	echo $email;
}
?>
<div class="container" id="main-content">
	<div class="col-md-12">
		<div class="row">
			<form action="index.php?page=listbenutzer" method="post">
				<table class="table">
					<thead>
						<tr>
							<th width="30%">Benutzername</th>
							<th width="50%">Email-Adresse</th>
							<th width="20%">Aktionen</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$db = new SchnitzelDB();
						$db->connect();
						$users = $db->listUsers("ASC");
						$id = 0;
						foreach ($users as $user) {
							echo "<tr>";
							echo "<td><input type=\"text\" class=\"form-control\" name=\"usernameInput" . $user["id"] . "\" id=\"usernameInput" . $user["id"] . "\" value=\"" . $user["username"] . "\"></td>";
							echo "<td><input type=\"text\" class=\"form-control\" name=\"emailInput" . $user["id"] . "\" id=\"emailInput" . $user["id"] . "\" value=\"" . $user["email"] . "\"></td>";
							echo "<td class=\"listbenutzer-buttons\">";
							echo "<button name=\"update\" value=\"" . $user["id"] . "\" type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-floppy-o\" aria-hidden=\"true\"></i></button>&nbsp;";
							echo "<button name=\"delete\" value=\"" . $user["id"] . "\" type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></button>";
							echo "</td>";
							echo "</tr>";
							$id++;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td>
								<input type="text" name="newUsername" class="form-control" id="newUserUsername" placeholder="Benutzername">
							</td>
							<td>
								<input type="text" name="newEmail" class="form-control" id="newUserEmail" placeholder="Email-Adresse">
							</td>
							<td class="listbenutzer-buttons">
								<button type="submit" name="create" class="btn btn-primary"><i class="fa fa-user-plus" aria-hidden="true"></i></button>
							</td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>
