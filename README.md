1. Deutscher Schnitzelverein e.V.
=======================
Dies ist der Quellcode der Webseite des 1. Deutschen Schnitzelvereins e.V.

Aufbau
============
* index.php : Grundseite

* api.php : API (kann in Settings.php (de-)aktiviert werden)

* installer.php : Installationsskript (sollte nach einmaligem Ausführen gelöscht werden)

* ical.php : Termine als ical-Datei

* SchnitzelDB.php : Abkapslung der Datenbank mit Funktionen zum Lesen, Erstellen, Bearbeiten und Löschen

* SchnitzelUtils.php : Sammlung mehrfach verwendeter Funktionen wie Token- oder Passwort-Erzeugung

* bootstrap/ : Angepasstes Bootstrap

* content/ : Inhaltsseiten

* css/ : CSS-Dateien (aus less kompiliert)

* documents/ : Vereinsdokumente, etwa Formulare

* less/ : Less-Dateien, eine pro Seite aus content/

  * less/schnitzel.less : wird für alle Seiten verwendet, etwa Banner, Navbar, Panels, ...

* resources/ : Ressourcen, etwa Bilder und Logo

Verwendete Frameworks
============
* Bootstrap
* Font-Awesome
* Trumbowyg
