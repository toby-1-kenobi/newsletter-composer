<?php

require_once 'db.php';
require_once 'environment.php';

// make sure the user is logged in
function login_ok() {
	
	session_start();
	
	//echo "<p>debug login A {$_SESSION['uid']}</p>";
	if (!isset($_SESSION['uid'])) return false;
	//echo '<p>debug login B</p>';
	
	$dbh = dbConnect();
	
	$q_check_login = $dbh->prepare ("SELECT COUNT(*) AS login_ok FROM Users WHERE name = :name AND password = MD5(:password)");
	$q_check_login->bindParam(':name', $_SESSION['uid']);
	$q_check_login->bindParam(':password', $_SESSION['pwd']);
	$q_check_login->execute();
	return $q_check_login->fetchColumn();
}

//This functions checks and makes sure the email address that is being added to database is valid in format. 
function check_email_address($email) {
  // First, we check that there's one @ symbol, and that the lengths are right
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
     if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
      return false;
    }
  }  
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}
?>
