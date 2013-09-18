<?php

require_once 'db.php';
require_once 'common.php';


// check user is logged in
if (login_ok() == 1) {
	
	$dbh = dbConnect();
	
	$dbh = null;
	
} else {
	echo '<p>Fail. Could not verify user. Login again.</p>';
}
