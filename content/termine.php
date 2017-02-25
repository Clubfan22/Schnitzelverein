<link href="css/termine.css" rel="stylesheet">
<?php if($isLoggedIn){
	if (isset($_POST["delete"])){
		$id = filter_input(INPUT_POST, 'delete', FILTER_SANITIZE_NUMBER_INT);
		$db = new SchnitzelDB();
		$db->connect();
		$res = $db->deleteEvent($id);
		if ($res == false) {
			echo "<p>Beim Entfernen des Termins mit der ID " . $id . " ist ein Fehler aufgetreten</p>";
		}
	}
}
?>
<div class="container" id="main-content">
	<form action="index.php?page=termine" method="post">
	<h1>
		Kommende Termine:
		<?php
		if ($isAdministrator){
			echo "<a href=\"index.php?page=edittermin\" class=\"btn btn-primary\" id=\"createBtn\" role=\"button\"><i class=\"fa fa-calendar-plus-o\" aria-hidden=\"true\"></i></a>";
		}
		?>
	</h1>
		<?php
		$db = new SchnitzelDB();
		$db->connect();
		$upcomingEvents = $db->listEventsByTime('ASC', 0, 'later');
		SchnitzelUtils::displayEvents($upcomingEvents, 'html', $isAdministrator);
		?>
		<p>Du willst immer auf dem neusten Stand sein, aber nicht täglich eine weitere Website aufrufen? Kein Problem, die Termine des Schnitzelvereins können ganz einfach in deinen Google-Kalender oder andere Kalender-Applikationen importiert werden.<a class="subscribe-to-calendar" href="http://www.google.com/calendar/render?cid=https://schnitzelverein.de/ical.php" target="_blank"><i class="fa fa-google" aria-hidden="true"></i></a><a class="subscribe-to-calendar" href="webcal://schnitzelverein.de/ical.php" target="_blank"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></a></p>
	<h1>Vergangene Termine:</h1>
	<?php
	$db = new SchnitzelDB();
	$db->connect();
	$earlierEvents = $db->listEventsByTime('DESC', 0, 'earlier');
	SchnitzelUtils::displayEvents($earlierEvents, 'html', $isAdministrator);
	?>
	</form>
</div>