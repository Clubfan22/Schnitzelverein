<link href="css/termine.css" rel="stylesheet">
<link href="css/edittermin.css" rel="stylesheet">
<link rel="stylesheet" href="trumbowyg/dist/ui/trumbowyg-schnitzel.css">
<?php
global $isLoggedIn;
if (!$isLoggedIn) {
	die();
}

if (isset($_POST["store"])) {
	//Termin soll geändert/erstellt werden
	if (isset($_GET["id"])) {
		//Bestehender Termin wird geändert
			$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
			if ($id > 0) {
				$event = array();
				$event["id"] = $id;
				$date = filter_input(INPUT_POST, "dateInput", FILTER_SANITIZE_STRING);
				$time = filter_input(INPUT_POST, "timeInput", FILTER_SANITIZE_STRING);
				$event_date = new DateTime($date." ".$time, new DateTimeZone("Europe/Berlin"));
				$event["event_date"] = $event_date->format(DATE_ATOM);
				$event["location"] = filter_input(INPUT_POST, "locationInput", FILTER_SANITIZE_STRING);
				$event["street"] = filter_input(INPUT_POST, "streetInput", FILTER_SANITIZE_STRING);
				$event["city"] = filter_input(INPUT_POST, "cityInput", FILTER_SANITIZE_STRING);
				$event["text"] = filter_input(INPUT_POST, "textInput", FILTER_UNSAFE_RAW);
				//Preload falls Fehler beim Speichern auftritt
				$event["date"] = $date;
				$event["time"] = $time;
				$db = new SchnitzelDB();
				$db->connect();
				$res = $db->updateEvent($event);
				if ($res == false){
					echo "<p>Bearbeiten des Termins mit der ID ".$id." fehlgeschlagen!</p>";
				} else {
					$id = $res;
					echo "<script type=\"text/javascript\">window.location.href = \"http://ammon.diskstation.me/Schnitzelverein/index.php?page=termine\";</script>";
				}
			}
	} else {
		//Neuer Termin wird angelegt
		$event = array();
		$date = filter_input(INPUT_POST, "dateInput", FILTER_SANITIZE_STRING);
		$time = filter_input(INPUT_POST, "timeInput", FILTER_SANITIZE_STRING);
		$event_date = new DateTime($date." ".$time, new DateTimeZone("Europe/Berlin"));
		$event["event_date"] = $event_date->format(DATE_ATOM);
		$event["location"] = filter_input(INPUT_POST, "locationInput", FILTER_SANITIZE_STRING);
		$event["street"] = filter_input(INPUT_POST, "streetInput", FILTER_SANITIZE_STRING);
		$event["city"] = filter_input(INPUT_POST, "cityInput", FILTER_SANITIZE_STRING);
		$event["text"] = filter_input(INPUT_POST, "textInput", FILTER_UNSAFE_RAW);
		$event["date"] = $event_date->format("Y-m-d");
		$event["time"] = $event_date->format("H:i");
		$db = new SchnitzelDB();
		$db->connect();
		$res = $db->createEvent($event);
		if ($res == false){
			echo "<p>Anlegen des Termins fehlgeschlagen!</p>";
		} else {
			echo "<script type=\"text/javascript\">window.location.href = \"http://ammon.diskstation.me/Schnitzelverein/index.php?page=termine\";</script>";
		}
	}
} else {
	//Anzeigen des Formulars ohne direktes Speichern
	if (isset($_GET["id"])) {
		//Termin soll geändert werden
		//Vorladen der bestehenden Daten
		$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
		$db = new SchnitzelDB();
		$db->connect();
		$event = $db->selectEventByID($id);
		$date = new DateTime($event["event_date"]);
		$event["date"] = $date->format("Y-m-d");
		$event["time"] = $date->format("H:i");
	}
}
?>
<div class="container" id="main-content">

<form class="form-horizontal" method="post" target="_self" action="index.php?page=edittermin<?php if (isset($id)) {
	echo "&id=" . $id;
} ?>">
	<div class="row">
		<div class="col-lg-12">
			<div class="termin">
				<div class="col-lg-12 termin-date">
					<h3><input type="text" class="form-control" id="dateInput" placeholder="JJJJ-MM-TT" name="dateInput"<?php if (isset($event["date"])) {
						echo " value=\"" . $event["date"] . "\"";
					} ?>><button type="submit" name="store" class="btn btn-primary" id="storeBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button></h3>					
				</div>
				<div class="termin-heading col-md-4 col-sm-12">
					<h4>
						<input type="text" size="5" placeholder="SS:MM" class="form-control" name="timeInput" id="timeInput"<?php if (isset($event["time"])) {
							echo " value=\"" . $event["time"] . "\"";} ?>> <label for="timeInput">Uhr</label>
					</h4>
					<address>
						<strong><input type="text" class="form-control" placeholder="Örtlichkeit" name="locationInput"<?php if (isset($event["location"])) {
							echo " value=\"" . $event["location"] . "\"";
						} ?>></strong><br>
						<input type="text" class="form-control" placeholder="Straße Hausnummer" name="streetInput"<?php if (isset($event["street"])) {
							echo " value=\"" . $event["street"] . "\"";
						} ?>><br>
						<input type="text" class="form-control" placeholder="PLZ Ort" name="cityInput"<?php if (isset($event["city"])) {
							echo " value=\"" . $event["city"] . "\"";
						} ?>><br>					
					</address>
				</div>
				<div class="termin-body col-md-8 col-sm-12">
					<textarea id="textInput" placeholder="Beschreibungstext mit Tagesordnung" name="textInput"><?php if (isset($event["text"])) {
							echo $event["text"];
						} ?></textarea>
				</div>
				<div style="display: block; clear: both;"></div>
			</div>
		</div>
	</div>	
</form>
</div>
<script type="text/javascript">$(window).load(function(){
	$('#textInput').trumbowyg({
    lang: 'de',
	btns: [
        ['viewHTML'],
        ['formatting'],
        'btnGrp-semantic',
        ['superscript', 'subscript'],
        ['link'],
        'btnGrp-justify',
        'btnGrp-lists',
        ['removeformat'],
        ['fullscreen']
    ],
	resetCss: true,
	autogrow: true
});
});</script>
<script src="trumbowyg/dist/trumbowyg.min.js"></script>
<script type="text/javascript" src="trumbowyg/dist/langs/de.min.js"></script>