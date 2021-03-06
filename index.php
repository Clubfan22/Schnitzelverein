<?php
ob_start();
ini_set('auto_detect_line_endings', true);
include 'SchnitzelUtils.php';
$token = filter_input(INPUT_COOKIE, 'token', FILTER_UNSAFE_RAW);
$stay = filter_input(INPUT_COOKIE, 'stay', FILTER_SANITIZE_NUMBER_INT);
$isLoggedIn = SchnitzelUtils::isLoggedIn($token);
$isAdministrator = false;
if ($isLoggedIn){
	SchnitzelUtils::keepSessionAlive($token, $stay);
	$isAdministrator = SchnitzelUtils::isAdministrator($token);
}
$navigation = [
	["page" => "übersicht", "title" => "Übersicht", "visibility" => "lg", "ending" => "php", "description" => "Du liebst Schnitzel und Geselligkeit? Wir vom 1. Deutschen Schnitzelverein e.V. auch!"],
	//["page" => "geschichte", "title" => "Geschichte", "icon" => "file-text-o"],
	["page" => "termine", "title" => "Termine", "icon" => "calendar", "ending" => "php", "description" => "Du willst kein Treffen des Schnitzelvereins mehr verpassen? Hier gibt es alle bisherigen und zukünftigen Termine."],
	["page" => "mitglied", "title" => "Mitglied werden!", "ending" => "html", "description" => "Du willst Mitglied im 1. Deutschen Schnitzelverein e.V. werden? Dann nichts wie los und hier anmelden!"]
];
$other_pages = [
	["page" => "impressum", "title" => "Impressum", "ending" => "html", "description" => "1. Deutscher Schnitzelverein e.V. Nürnberger Straße 2 91244 Reichenschwand"],
	["page" => "login", "title" => "Anmelden", "ending" => "php"],
	["page" => "logout", "title" => "Abmelden", "ending" => "php"],
	["page" => "edittermin", "title" => "Termin bearbeiten", "ending" => "php"],
	["page" => "editpost", "title" => "Post bearbeiten", "ending" => "php"],
	['page' => 'datenschutz', 'title' => 'Datenschutzerklärung', 'ending' => 'html']//,
	//["page" => "registration", "title" => "Registrieren", "ending" => "php"],
	//["page" => "aktivierung", "title" => "Aktivieren", "ending" => "php"]
];
if ($isLoggedIn){
	$restricted_pages = [
		["page" => "listbenutzer", "title" => "Benutzerverwaltung", "icon" =>"users", "ending" => "php"]
	];
	$navigation = array_merge($navigation, $restricted_pages);
}

$pages = array_merge($navigation, $other_pages);
if (isset($_GET["page"])) {
	$page = strtolower($_GET["page"]);
	if (!in_array_2d($page, $pages, "page")) {
		$page = "übersicht";
	}
} else {
	$page = "übersicht";
}
$title = get_value_from_page($page, $pages, "title");
?><!DOCTYPE html>
<html lang="de" prefix="og: http://ogp.me/ns#">
    <head>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans|Kaushan+Script' rel='stylesheet' type='text/css'>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="<?php echo get_value_from_page($page, $pages, "description"); ?>">
		<meta property="og:title" content="<?php echo $title. " - 1. Deutscher Schnitzelverein e.V." ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:image" content="resources/logo_full.png" />
        <title><?php echo $title. " - 1. Deutscher Schnitzelverein e.V." ?></title>
		<!-- Bootstrap -->
		<link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<link href="icomoon/style.css" rel="stylesheet">
		<link href="css/schnitzel.css" rel="stylesheet">

		<link rel="icon" href="resources/logo_negativ_256.png">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
    </head>
    <body>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="bootstrap/dist/js/bootstrap.min.js"></script>
		<!--<script src="bootstrap/dist/js/button.js"></script>
		<script src="bootstrap/dist/js/collapse.js"></script>
		<script src="bootstrap/dist/js/dropdown.js"></script> -->
		<div id="wrap">
			<div class="visible-lg" id="banner">
				<div id="logo">
					<a href="index.php">
						<img src="resources/logo_negativ_256.png" alt="Logo" height="128" width="128">
					</a>
				</div>		
				<div class="banner-container">
					<span class="banner-title">1. Deutscher Schnitzelverein e.V.</span>
				</div>
			</div>
			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<div class="navbar-header hidden-lg">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse-1" aria-expanded="false">
							<span class="sr-only">Navigation wechseln</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="index.php"><img class="hidden-lg" id="navbar-logo" src="resources/logo_256.png" alt="Logo">
							<span class="navbar-title">1. Deutscher Schnitzelverein e.V.</span>
							<span class="navbar-title-short">Schnitzelverein e.V.</span>
						</a>					
					</div>
					<div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<?php foreach ($navigation as $element) { ?>
								<li <?php
								echo "class=\"";
								if ($element['page'] == $page) {
									echo "active ";
								};
								if (isset($element['visibility'])) {
									echo "visible-" . $element['visibility'];
								};
								echo "\"";
								?>>
									<a href="<?php echo ucfirst($element['page']); ?>"> <?php
										if (isset($element['icon'])) {
											echo "<i class=\"fa fa-" . $element['icon'] . " visible-sm\" aria-hidden=\"true\"></i>";
										};
										echo "<span";
										if (isset($element['icon'])) {
											echo " class=\"hidden-sm\"";
										} echo ">" . $element['title'] . "</span>";
										if ($element['page'] == $page) {
											echo "<span class=\"sr-only\">(current)</span>";
										};
										?></a>
								</li>
							<?php }; ?>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li class="nav-divider"></li>
							<li>
								<a href="https://www.facebook.com/Schnitzelverein" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i><span class="hidden-sm social-text"> Facebook</span></a>
							</li>
							<li>
								<a href="https://www.instagram.com/schnitzelverein/" target="_blank"><i class="icon-instagram-square-simple" aria-hidden="true"></i><span class="hidden-sm social-text"> Instagram</span></a>
							</li>
							<li>
								<a href="https://twitter.com/Schnitzelverein" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i><span class="hidden-sm social-text"> Twitter</span></a>
							</li>
						</ul>
					</div>

				</div>
			</nav>
			<?php
//Einbinden der eigentlich Content-Pages
			include ('content/' . $page . '.' . get_value_from_page($page, $pages, "ending"));
			?>
		</div>
		<div id="footer">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-12 col-md-4 col-sm-6 footer-text">
						<span>© 2016 - <?php echo date('Y') ?> 1. Deutscher Schnitzelverein e.V.</span>
					</div>					
					<div class="col-xs-4 col-md-3 col-sm-6 footer-text">
                    	<a href="Impressum">Impressum</a>
                    </div>
                    <div class="col-xs-4 col-md-3 col-sm-3 footer-text">
                    	<a href="Datenschutz">Datenschutz</a>
                    </div>
					<div class="col-xs-4 col-md-2 col-sm-3 footer-text">
						<?php
						if (!$isLoggedIn){
						echo "<a href=\"index.php?page=login\">Anmelden</a>";
						} else {
						echo "<a href=\"index.php?page=logout\">Abmelden</a>";
						}
						?>
					</div>
				</div>
			</div>
		</div>

    </body>
</html>
<?php
ob_end_flush();
function in_array_2d($needle, $haystack, $key) {
	foreach ($haystack as $item) {
		if ($item[$key] == $needle) {
			return true;
		}
	}
	return false;
}

function get_value_from_page($page, $haystack, $property) {
	foreach ($haystack as $item) {
		if ($item["page"] == $page) {
			return $item[$property];
		}
	}
	return false;
}
