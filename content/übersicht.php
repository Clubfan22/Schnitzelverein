<link href="css/übersicht.css" rel="stylesheet">
<div class="container" itemscope itemtype="http://schema.org/LocalBusiness" id="main-content">
	<div class="col-lg-12">
		<div class="col-lg-3 visible-lg visible-md übersicht-box-float">
			<span style="font-size: 26pt;">35</span> <span style="font-size: 16pt;">Mitglieder,</span>
			<br>
			<span style="font-size: 26pt;">1</span> <span style="font-size: 16pt;">Leidenschaft:</span>
			<br>
			<span style="font-size: 36pt;">Schnitzel</span>
		</div>
		<h1 id="main-heading">Froh Schnitzel!</h1>
		<p><strong>Herzlich Willkommen auf der Website des 1. Deutschen Schnitzelvereins e.V.!</strong><br>
			Als Idee an einem Geburtstag geboren, sind wir nun ein rechtmäßig eingetragener Verein, der sich um das gemeinsame Zelebrieren, die Erhaltung und Förderung des Kulturguts Schnitzel sorgt und kümmert.
			Dieses Ziel versuchen wir vor allem durch regelmäßige Treffen, aber auch organisierte Freizeitaktivitäten, zu erfüllen.
			Gerade in einer Zeit des ständigen Voranschreitens des Vegetarismus geben wir dem Lieblingsessen der Deutschen eine Stimme!<br>
			Besonders wichtig ist uns die Kollegialität innerhalb des Vereins, denn ein ohnehin schon wohlschmeckendes Schnitzel wird durch echte Freunde zu einem wahrlich famosen Genuss.
			Neue Mitglieder (und solche, die es noch werden möchten) sind natürlich bei jedem Treffen willkommen. Die Termine hierzu können <a href="index.php?page=termine">hier</a> eingesehen werden.<br>
			Demnächst werden hier auch Bewertungen und Empfehlungen für Schnitzel im Nürnberger Land zu lesen sein.
		</p>
		<h2>Schnitzelgrüße von Instagram!</h2>
		<?php
			include 'InstagramFeed.php';
			include 'Settings.php';
			global $instagramUserId;
			InstagramFeed::displayRecentMedia($instagramUserId, 6);
		?>
		<meta itemprop="name" content="1. Deutscher Schnitzelverein e.V.">
		<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
		<meta itemprop="streetAddress" content="Nürnberger Straße 2">
		<meta itemprop="addressLocality" content="Reichenschwand">
		<meta itemprop="addressRegion" content="Bayern">
		<meta itemprop="addressCountry" content="Deutschland">
		<meta itemprop="postalCode" content="91244"></span>
		<meta itemprop="url" content="https://schnitzelverein.de/">
		<meta itemprop="email" content="admin@schnitzelverein.de">
		<div class="col-lg-4 hidden-lg hidden-md übersicht-box">
			35 Mitglieder,<br>
			1 Leidenschaft:<br>
			Schnitzel
		</div>
	</div>
</div>
