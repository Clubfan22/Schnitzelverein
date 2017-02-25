<link href="css/posts.css" rel="stylesheet">
<link href="css/editpost.css" rel="stylesheet">
<link rel="stylesheet" href="trumbowyg/dist/ui/trumbowyg-schnitzel.css">
<?php
global $isAdministrator;
if (!$isAdministrator) {
	die();
}
if (isset($_POST["store"])) {
	//Post soll geändert/erstellt werden
	if (isset($_GET["id"])) {
		//Bestehender Post wird geändert
			$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
			if ($id > 0) {
				$post = array();
				$db = new SchnitzelDB();
				$db->connect();
				$user = $db->selectUserBySessionToken(filter_input(INPUT_COOKIE, 'token', FILTER_UNSAFE_RAW));
				$userid = $user["id"];
				$post["id"] = $id;				
				$event_date = new DateTime("now", new DateTimeZone("Europe/Berlin"));
				$post["release_date"] = $event_date->format(DATE_ATOM);
				$post["author_id"] = $userid;
				$post["title"] = filter_input(INPUT_POST, "titleInput", FILTER_SANITIZE_STRING);
				$post["text"] = filter_input(INPUT_POST, "textInput", FILTER_UNSAFE_RAW);				
				$res = $db->updatePost($post);
				if ($res == false){
					echo "<p>Bearbeiten des Posts mit der ID ".$id." fehlgeschlagen!</p>";
				} else {
					$id = $res;
					echo "<script type=\"text/javascript\">window.location.href = \"https://schnitzelverein.de/\";</script>";
				}
			}
	} else {
		//Neuer Post wird angelegt
		$post = array();
		$db = new SchnitzelDB();
		$db->connect();
		$user = $db->selectUserBySessionToken(filter_input(INPUT_COOKIE, 'token', FILTER_UNSAFE_RAW));
		$userid = $user["id"];
		$post_date = new DateTime("now", new DateTimeZone("Europe/Berlin"));
		$post["release_date"] = $post_date->format(DATE_ATOM);
		$post["author_id"] = $userid;
		$post["title"] = filter_input(INPUT_POST, "titleInput", FILTER_SANITIZE_STRING);
		$post["text"] = filter_input(INPUT_POST, "textInput", FILTER_UNSAFE_RAW);				
		$res = $db->createPost($post);
		if ($res == false){
			echo "<p>Anlegen des Posts fehlgeschlagen!</p>";
		} else {
			echo "<script type=\"text/javascript\">window.location.href = \"https://schnitzelverein.de/\";</script>";
		}
	}
} else {
	//Anzeigen des Formulars ohne direktes Speichern
	if (isset($_GET["id"])) {
		//Post soll geändert werden
		//Vorladen der bestehenden Daten
		$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
		$db = new SchnitzelDB();
		$db->connect();
		$post = $db->selectPostByID($id);	
	}
}
?>
<div class="container" id="main-content">

<form class="form-horizontal" method="post" target="_self" action="index.php?page=editpost<?php if (isset($id)) {
	echo "&id=" . $id;
} ?>">
	<div class="row">
		<div class="col-lg-12">
			<div class="post">
				<div class="col-lg-12 post-title">
					<h3><input type="text" class="form-control" id="titleInput" placeholder="Überschrift" name="titleInput"<?php if (isset($post["title"])) {
						echo " value=\"" . $post["title"] . "\"";
					} ?>><button type="submit" name="store" class="btn btn-primary" id="storeBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button></h3>					
				</div>				
				<div class="post-body col-md-12 col-sm-12">
					<textarea id="textInput" placeholder="Blogtext" name="textInput"><?php if (isset($post["text"])) {
							echo $post["text"];
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