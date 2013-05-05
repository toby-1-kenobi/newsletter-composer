<?php

require_once 'common.php';

if (login_ok() == 1) {
	
	$rel_user_path = 'users/' . $_SESSION['uid'] . '/';
	
	$htaccess_content = '<FilesMatch "\.(html|htm)$">';
	$htaccess_content .= "\nAuthType Basic";
	$htaccess_content .= "\nAuthName Protected";
	$htaccess_content .= "\nAuthUserFile {$_ENV['DOCUMENT_ROOT']}/{$rel_user_path}.htpasswd";
	$htaccess_content .= "\nRequire valid-user";
	$htaccess_content .= "\n</FilesMatch>";
	
	if ($_POST['action'] === 'set')
	{
		$access_file = fopen("{$_ENV['DOCUMENT_ROOT']}/{$rel_user_path}.htaccess", 'w');
		fputs($access_file, $htaccess_content);
		fclose($access_file);
		chmod("{$_ENV['DOCUMENT_ROOT']}/{$rel_user_path}.htaccess", 0644);
		
		$htpasswd_text = $_POST['username'] . ':{SHA}' . base64_encode(sha1($_POST['password'], TRUE));
		$password_file = fopen("{$_ENV['DOCUMENT_ROOT']}/{$rel_user_path}.htpasswd", 'w');
		fputs($password_file, $htpasswd_text);
		fclose($password_file);
		chmod("{$_ENV['DOCUMENT_ROOT']}/{$rel_user_path}.htpasswd", 0644);
		
		echo '<p>Access to your online content is <strong>protected</strong>.</p>';
		echo "<p>{$_POST['username']}<br />{$_POST['password']}</p>";
	}
	
	if ($_POST['action'] === 'unset')
	{
		unlink("{$_ENV['DOCUMENT_ROOT']}/{$rel_user_path}.htaccess");
		
		unlink("{$_ENV['DOCUMENT_ROOT']}/{$rel_user_path}.htpasswd");
		
		echo '<p>Access to your online content is <strong>public</strong>.</p>';
	}

} else {
	echo '<p>.htaccess not written. Could not verify user. Login again.</p>';
}
