<?php
global $token;
if ($token != ''){
	$db = new SchnitzelDB();
	$db->connect();
	$db->deleteSession($token);
	unset($_COOKIE['token']);
    setcookie('token', null, -1);
}
header('Location: index.php');
?>

