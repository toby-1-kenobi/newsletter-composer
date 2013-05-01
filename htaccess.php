<?php

require_once 'common.php';

if (login_ok() == 1) {
	
	$rel_user_path = 'users/' . $_SESSION['uid'] . '/';
	
	$htaccess_content = '<FilesMatch "\.(html|htm)$">';
	$htaccess_content .= "\nAuthType Basic";
	$htaccess_content .= "\nAuthName \"Access to this file is restricted\"";
	$htaccess_content .= "\nAuthUserFile {$_ENV['DOCUMENT_ROOT']}/$rel_user_path.htpasswd";
	$htaccess_content .= "\nrequire valid-user";
	$htaccess_content .= "\n</FilesMatch>";
	
	if ($_POST['action'] === 'set')
	{
		echo shell_exec("htpasswd -bc {$_ENV['DOCUMENT_ROOT']}/$rel_user_path.htpasswd {$_POST['username']} {$_POST['password']}");
	}

} else {
	echo '<p>.htaccess not written. Could not verify user. Login again.</p>';
}
