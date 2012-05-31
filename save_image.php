<?php
// receive a base64 encoded png image and save it as a file

require_once 'common.php';

// make sure the user is logged in first
if (login_ok()) {
		
	$data = $_POST['data'];

	//sort out the filename
	$file = $_POST['filename'];
	$info = pathinfo($file);
	$oldExtension = $info['extension'];
	$newExtension = $_POST['ext'];
	$filename =  basename($file, '.' . $oldExtension);

	// make sure it's a file name that doesn't already exist
	if (file_exists("./users/{$_SESSION['uid']}/images/" . $filename . '.' . $newExtension)) {
		$i = 1;
		while(file_exists("./users/{$_SESSION['uid']}/images/" . $filename . $i . '.' . $newExtension)) ++$i;
		$filename = $filename . $i;
	}
	
	//removing the "data:image/png;base64," part
	$uri =  substr($data, strpos($data, ",") + 1);
	
	// put the data to a file
	file_put_contents("./users/{$_SESSION['uid']}/images/" . $filename . '.' . $newExtension, base64_decode($uri));
	
	echo $filename . '.' . $newExtension;
	
}
?>