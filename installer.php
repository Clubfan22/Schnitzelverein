<?php
include 'SchnitzelDB.php';
include 'SchnitzelUtils.php';
$db = new SchnitzelDB();
$db->connect();
$db->createTables();
$user = array();
$user['username'] = "admin";
$salt= SchnitzelUtils::getToken(63);
$user['salt'] = $salt;
$user['password'] = SchnitzelUtils::hashPassword("admin", $salt);
$db->createUser($user);
echo "Bitte diese Datei sofort löschen";
?>

