<?php
$navigation = [
	["page" => "übersicht", "title" => "Übersicht", "visibility" => "lg"],
	["page" => "geschichte", "title" => "Geschichte", "icon" => "file-text-o"],
	["page" => "termine", "title" => "Termine", "icon" => "calendar"],
	["page" => "mitglied", "title" => "Mitglied werden!"]
];
$other_pages = [
	["page" => "impressum", "title" => "Impressum"]
];
$pages = array_merge($navigation, $other_pages);
if (isset($_GET["page"])) {
	$page = strtolower($_GET["page"]);
	if (!in_array_2d($page, $pages, "page")) {
		$page = "übersicht";
	}
} else {
	$page = "übersicht";
}
$title = get_title_from_page($page, $pages);
?><!DOCTYPE html>
<html lang="de">
    <head>
		<link href='https://fonts.googleapis.com/css?family=Rock+Salt|Open+Sans|Kaushan+Script' rel='stylesheet' type='text/css'>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title ?></title>
		<!-- Bootstrap -->
		<link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<link href="css/schnitzel.css" rel="stylesheet">

		<link rel="icon" href="resources/logo.png">

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
		<script src="bootstrap/dist/js/button.js"></script>
		<!--<script src="bootstrap/dist/js/collapse.js"></script>
		<script src="bootstrap/dist/js/dropdown.js"></script> -->
		<div id="wrap">
			<div class="visible-lg" id="banner">
				<div id="logo">
					<a href="index.php">
						<img src="resources/logo.png" alt="Logo" height="128" width="128">
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
						<a class="navbar-brand" href="index.php"><img class="hidden-lg" id="navbar-logo" src="resources/logo.png" alt="Logo">
							<span class="navbar-title">1. Deutscher Schnitzelverein e.V.</span>
							<span class="navbar-title-short">Schnitzelverein e.V.</span>
						</a>					
					</div>
					<div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<?php foreach ($navigation as $element) { ?>
								<li <?php
								echo "class=\"";
								if ($element[page] == $page) {
									echo "active ";
								};
								if (isset($element[visibility])) {
									echo "visible-" . $element[visibility];
								};
								echo "\"";
								?>>
									<a href="<?php echo "index.php?page=" . $element[page]; ?>"> <?php
										if (isset($element[icon])) {
											echo "<i class=\"fa fa-" . $element[icon] . " visible-sm\" aria-hidden=\"true\"></i>";
										};
										echo "<span";
										if (isset($element[icon])) {
											echo " class=\"hidden-sm\"";
										} echo ">" . $element[title] . "</span>";
										if ($element[page] == $page) {
											echo "<span class=\"sr-only\">(current)</span>";
										};
										?></a>
								</li>
							<?php }; ?>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li class="nav-divider"></li>
							<li>
								<a href="#"><i class="fa fa-facebook-square" aria-hidden="true"></i><span class="hidden-sm social-text"> Facebook</span></a>
							</li>
							<li>
								<a href="#"><i class="fa fa-instagram" aria-hidden="true"></i><span class="hidden-sm social-text"> Instagram</span></a>
							</li>
							<li>
								<a href="#"><i class="fa fa-twitter-square" aria-hidden="true"></i><span class="hidden-sm social-text"> Twitter</span></a>
							</li>
						</ul>
					</div>

				</div>
			</nav>
			<?php
//Einbinden der eigentlich Content-Pages
			include ('content/' . $page . '.html');
			?>
		</div>
		<div id="footer">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-6 col-sm-6 footer-text">
						<span>© 2016 1. Deutscher Schnitzelverein e.V.</span>
					</div>					
					<div class="col-md-6 col-sm-6 footer-text">
						<a href="index.php?page=impressum">Impressum</a>
					</div>
				</div>
			</div>
		</div>

    </body>
</html>
<?php

function in_array_2d($needle, $haystack, $key) {
	foreach ($haystack as $item) {
		if ($item[$key] == $needle) {
			return true;
		}
	}
	return false;
}

function get_title_from_page($page, $haystack) {
	foreach ($haystack as $item) {
		if ($item["page"] == $page) {
			return $item["title"];
		}
	}
	return "Error! Title not found!";
}
