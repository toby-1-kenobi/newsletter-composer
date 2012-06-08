<?php
/**
 * Password Reset - Used for allowing a user to reset password
 * 
 * @author Dan <dan@danbriant.com>
 */
// modified by Toby Anderson

require_once '../php/db.php';

//this function will display error messages in alert boxes, used for login forms so if a field is invalid it will still keep the info
//use error('foobar');
function error($msg) {
    ?>
    <html>
    <head>
    <script language="JavaScript">
    <!--
        alert("<?=$msg?>");
        history.back();
    //-->
    </script>
    </head>
    <body>
    </body>
    </html>
    <?
    exit;
}


if (isset($_POST['submit'])) {
	
	if ($_POST['forgotpassword']=='') {
		error('Please Fill in Email.');
	}
	if(get_magic_quotes_gpc()) {
		$forgotpassword = htmlspecialchars(stripslashes($_POST['forgotpassword']));
	} 
	else {
		$forgotpassword = htmlspecialchars($_POST['forgotpassword']);
	}
	//Make sure it's a valid email address, last thing we want is some sort of exploit!
	if (!check_email_address($_POST['forgotpassword'])) {
  		error('Email Not Valid - Must be in format of name@domain.tld');
	}
    // Lets see if the email exists
    $sql = "SELECT COUNT(*) FROM members WHERE user_email = '$forgotpassword'";
    $result = mysql_query($sql)or die('Could not find member: ' . mysql_error());
    if (!mysql_result($result,0,0)>0) {
        error('Email Not Found!');
    }

	//Generate a RANDOM MD5 Hash for a password
	$random_password=md5(uniqid(rand()));
	
	//Take the first 8 digits and use them as the password we intend to email the user
	$emailpassword=substr($random_password, 0, 8);
	
	//Encrypt $emailpassword in MD5 format for the database
	$newpassword = md5($emailpassword);
	
        // Make a safe query
       	$query = sprintf("UPDATE `members` SET `user_password` = '%s' 
						  WHERE `user_email` = '$forgotpassword'",
                    mysql_real_escape_string($newpassword));
					
					mysql_query($query)or die('Could not update members: ' . mysql_error());

//Email out the infromation
$subject = "Your New Password"; 
$message = "Your new password is as follows:
---------------------------- 
Password: $emailpassword
---------------------------- 
Please login and change your password ASAP

This email was automatically generated."; 
                       
          if(!mail($forgotpassword, $subject, $message,  "FROM: $site_name <$site_email>")){ 
             die ("Sending Email Failed, Please Contact Site Admin! ($site_email)"); 
          }else{ 
                error('New Password Sent!.');
         } 
		
	}
	
else {
?>
      <form name="forgotpasswordform" action="" method="post">
        <table border="0" cellspacing="0" cellpadding="3" width="100%">
          <caption>
          <div>Forgot Password</div>
          </caption>
          <tr>
            <td>Email Address:</td>
            <td><input name="forgotpassword" type="text" value="" id="forgotpassword" /></td>
          </tr>
          <tr>
            <td colspan="2" class="footer"><input type="submit" name="submit" value="Reset my password" /></td>
          </tr>
        </table>
      </form>
      <?
}
?>
