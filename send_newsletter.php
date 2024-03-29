<?php

require_once "Mail.php";

$content = '';
if ($_POST['email_file'])
{
	// read the email content that has been generated
	$content = file_get_contents($_POST['email_file']);
} else {
	// or it might have already been read from the file and posted to this script
	$content = $_POST['email_content'];
}

if ($content) {
	
	// decode strings encoded using javascript encodeURIComponent()
	$_POST['name'] = preg_replace('/%([0-9a-f]{2})/ie', "chr(hexdec('\\1'))", $_POST['name']);
	$_POST['subject_line'] = preg_replace('/%([0-9a-f]{2})/ie', "chr(hexdec('\\1'))", $_POST['subject_line']);
	$_POST['greetingA'] = preg_replace('/%([0-9a-f]{2})/ie', "chr(hexdec('\\1'))", $_POST['greetingA']);
	$_POST['greetingB'] = preg_replace('/%([0-9a-f]{2})/ie', "chr(hexdec('\\1'))", $_POST['greetingB']);
	
	// put the name and greeting in the right spot in the email
	$content = str_replace('%NAME%', $_POST['name'], $content);
	$content = str_replace('%EMAIL ADDRESS%', $_POST['to_address'], $content);
	$content = str_replace('%PERSONAL A%', $_POST['greetingA'], $content);
	$content = str_replace('%PERSONAL B%', $_POST['greetingB'], $content);
	
	// prepare the email headers
	$headers = array (
		'MIME-Version' => '1.0',
		'Content-type' => 'text/html; charset=utf-8',
		'From' => $_POST['from_address'],
   	'To' => $_POST['to_address'],
   	'Subject' => $_POST['subject_line']
   );
	//$headers  = 'MIME-Version: 1.0' . PHP_EOL;
	//$headers .= 'Content-type: text/html; charset=utf-8' . PHP_EOL;
	//$headers .= "From: <{$_POST['from_address']}>";
	
	//$headers .= "Return-path: <{$_POST['from_address']}>" . PHP_EOL;
   //$headers .= 'X-Mailer: PHP/' . phpversion();
   
   // set up the SMTP settings
   // assume ssl protocal and normal password authentication
   $smtp = Mail::factory('smtp',
   array ('host' => 'ssl://' . $_POST['smtp_host'],
     'port' => $_POST['smtp_port'],
     'auth' => true,
     'username' => $_POST['smtp_user'],
     'password' => $_POST['smtp_pass']));
	
	// send the email
	//$sent = mail($_POST['to_address'], $_POST['subject_line'], $content, $headers);
	//$sent = mail('toby.anderson@gmail.com', 'test php mail', 'this is a test');
	$sent = $smtp->send($_POST['to_address'], $headers, $content);
	
	if (PEAR::isError($sent)) {
		$result = $_POST['to_address'] . ' ' . $sent->getMessage();
	} else {
		$result = 'Sent.';
	}

} else {
	// we couldn't read the email content
	$result = 'Fail: could not read email content.';
}

/*
// need to escape ampersand for sed
$name = addcslashes($_POST['name'], '&');       
$command = "cat {$_POST['emailFile']} | sed 's/%SALUTATION%/$name/g' | ./sendEmail -f {$_POST['from_address']} -t {$_POST['to_address']} -u {$_POST['subject_line']} -s smtp.gmail.com -o tls=yes -xu {$_POST['google_name']} -xp {$_POST['google_pass']}";
exec($command);
*/

echo "$result";
?>
