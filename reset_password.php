<?php
/**
 * Password Reset - Used for allowing a user to reset password
 * 
 * @author Dan <dan@danbriant.com>
 */
// modified by Toby Anderson

require_once '../php/db.php';

?>
<!DOCTYPE html>
<html>
<head>

<title>Newsletter Composer</title>

<meta name="author" content="Toby Anderson" />
<meta name="description" content="Reset user password for newsletter composer"/>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="content-style-type" content="text/css"/>
<meta http-equiv="expires" content="0"/>

<link href="css/newsletterComposer.css?<?php echo time(); ?>" rel="stylesheet" type="text/css"/>

</head>

<body>

<h1>Newsletter Composer</h1>
<h2>Reset Password</h2>

<?php


if (!isset($_POST['email_sent'])) {
	
?>
  <form name="forgotpasswordform" action="reset_password.php" method="post">
	<input type="hidden" name="email_sent" value="1" />
    <div><label for="email">email address:</label>
    <input type="email" required="required" name="email" /></div>
    <div><input type="submit" value="reset password" /></div>
  </form>
<?

} else {
	echo '<p>debug A</p>';
	
	if(get_magic_quotes_gpc()) {
		$user_email = htmlspecialchars(stripslashes($_POST['email']));
	} 
	else {
		$user_email = htmlspecialchars($_POST['email']);
	}
	echo "<p>$user_email</p>";
	
    // Lets see if the email exists
    $q_email_exists = $dbh->prepare("SELECT COUNT(*) AS email_exists FROM Users WHERE email = :email");
    $q_email_exists->bindParam(':email', $user_email);
    $q_email_exists->execute();
    $email_exists = $q_email_exists->fetchColumn();
    echo "<p>debug $email_exists</p>";
    if (!$email_exists) {
		
		echo '<p>That email address is not registered in our system!</p>';
		echo '<p><a href="reset_password.php">Go back</a></p>';
		
	} /*else {
		
		//Generate a RANDOM MD5 Hash for a password
		$random_password=md5(uniqid(rand()));
		
		//Take the first 8 digits and use them as the password we intend to email the user
		$emailpassword=substr($random_password, 0, 8);
		
		$q_ch_pwd = $dbh->prepare("UPDATE Users SET password=MD5(:password) WHERE email=:email");
		$q_ch_pwd->bindParam(':email', $forgotpassword);
		$q_ch_pwd->bindParam(':password', $emailpassword);
		$q_ch_pwd->execute();
		if ($q_ch_pwd->rowCount() == 0) {
			echo '<p>Password Update Failed!</p>';
			echo '<p><a href="reset_password.php">Go back</a></p>';
		} else {
			
			//Email out the information
			$subject = "Your New Password"; 
$message = "Your new password is as follows:
---------------------------- 
Password: $emailpassword
---------------------------- 
Please login and change your password ASAP

This email was automatically generated."; 
                       
			if(!mail($forgotpassword, $subject, $message,  "FROM: Toby Anderson <toby_anderson@sil.org>")){
				echo '<p> Your password was changed, but we were unable to send you the email with your new password. Please Contact Site Admin! (toby_anderson@sil.org)</p>'; 
			} else {
				echo '<p>Your new password was sent in an email.</p>';
				echo '<p><a href="index.php">Go back to Newsletter Composer</a></p>';
			}
		}
	}*/
}
?>
</body>
</html>
