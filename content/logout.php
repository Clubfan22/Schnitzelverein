<?php
global $token;
if ($token != ''){
	$db = new SchnitzelDB();
	$db->connect();
	$db->deleteSession($token);
	unset($_COOKIE['token']);
    setcookie('token', '', -1);
	unset($_COOKIE['stay']);
    setcookie('stay', '', -1);
}
header('Location: index.php');
?>

