<?php
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=Termine_Schnitzelverein.ics');

include "SchnitzelDB.php";
echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:https:/schnitzelverein.de/Termine\r\n";
echo "METHOD:PUBLISH\r\n";

$db = new SchnitzelDB();
$db->connect();
$events = $db->listEventsByTime("ASC", 0, "both");
foreach($events as $event){
	echo "BEGIN:VEVENT\r\n";
	echo "UID:ammon.marco@t-online.de\r\n";
	echo "ORGANIZER:CN=\"1. Deutscher Schnitzelverein e.V.\":MAILTO:ammon.marco@t-online.de\r\n";
	echo "LOCATION:".$event["location"]." ".$event["street"]." ".$event["city"]."\r\n";
	echo "SUMMARY:Treffen 1. Deutscher Schnitzelverein\r\n";
	echo "DESCRIPTION;ALTREP=\"https://schnitzelverein.de/Termine#event-".$event["id"]."\":".$event["text"]."\r\n";
	echo "X-ALT-DESC;FMTTYPE=text/html:".$event["text"]."\r\n";
	echo "CLASS:PUBLIC\r\n";
	//TODO: Timestamp richtig konvertieren
	$startDate = new DateTime($event["event_date"], new DateTimeZone("Europe/Berlin"));	
	$startDate->setTimezone(new DateTimeZone("Etc/UTC"));
	echo "DTSTART:".$startDate->format("Ymd\THis\Z")."\r\n";
	$endDate = $startDate->add(new DateInterval('PT3H'));
	echo "DTEND:".$endDate->format("Ymd\THis\Z")."\r\n";
	//Was bedeutet das?
	echo "DTSTAMP:20060812T125900Z\r\n";
	echo "END:VEVENT\r\n";
}
echo "END:VCALENDAR\r\n";

