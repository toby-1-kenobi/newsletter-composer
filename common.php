<?php

require_once '../php/db.php';

// make sure the user is logged in
function login_ok() {
	
	session_save_path(realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../sessions'));
	session_start();
	
	if (!isset($_SESSION['uid'])) return false;
	
	$dbh = dbConnect();
	
	$q_check_login = $dbh->prepare ("SELECT COUNT(*) AS login_ok FROM Users WHERE name = :name AND password = MD5(:password)");
	$q_check_login->bindParam(':name', $_SESSION['uid']);
	$q_check_login->bindParam(':password', $_SESSION['pwd']);
	$q_check_login->execute();
	return $q_check_login->fetchColumn();
}
?>
