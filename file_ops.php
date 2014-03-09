<?php

require_once 'common.php';

// check user is logged in
if (login_ok() == 1) {
	
	$rel_user_path = 'users/' . $_SESSION['uid'] . '/';
	
	if (strcmp($_POST['task'], 'list_files') == 0)
	{
		$handle = opendir($rel_user_path);
	
		// Make an array containing the files in the user's directory
		$files = array();
		while ($file = readdir($handle)) 
		{ 
			$files[] = $file; 
		} 
		closedir($handle);
		
		echo json_encode($files);
	}
	
	else
	{
		echo "<p>Fail. unrecognised task: {$_POST['task']}</p>";
	}
	
} else {
	echo '<p>Fail. Could not verify user. Login again.</p>';
}
