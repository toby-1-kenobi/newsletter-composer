<?php
require_once 'Excel/reader.php';
require_once 'db.php';

?>
<!DOCTYPE html>
<html>
<head>

<title>Newsletter Composer</title>

<meta name="generator" content="Bluefish 2.2.2" />
<meta name="author" content="Toby Anderson" />
<meta name="description" content="Generate and Send a newsletter"/>
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW"/>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8"/>
<meta http-equiv="content-style-type" content="text/css"/>
<meta http-equiv="expires" content="0"/>

<!--link href="css/smoothness/jquery-ui-1.10.0.custom.css" rel="stylesheet" type="text/css"/-->
<link href="http://code.jquery.com/ui/1.10.2/themes/ui-lightness/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link href="css/newsletterComposer.css?<?php echo time(); ?>" rel="stylesheet" type="text/css"/>

<?php

session_save_path('/home/users/web/b1356/ipg.newslettercomposerne/cgi-bin/tmp');
session_start();

// get the user credentials from either the existing session or the login post
$uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : $_POST['uid'];
$pwd = isset($_SESSION['pwd']) ? $_SESSION['pwd'] : $_POST['pwd'];

if (isset($uid)) {
	echo "<script type=\"text/javascript\">var userName = '$uid';</script>";
}

?>

<script src="js/json2.js" type="text/javascript"></script>
<script src="js/jquery-1.6.2.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.10.0.custom.js" type="text/javascript"></script>
<script src="js/jquery.cookies.2.2.0.js" type="text/javascript"></script>
<!--<script src="js/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>-->
<script src="js/newsletterComposer.js?<?php echo time(); ?>" type="text/javascript"></script>

</head>


<body>

<h1>Newsletter Composer</h1>

<p><a href="instructions.html" >Instructions</a> <a href="https://docs.google.com/forms/d/1cQoTwTyWFGSvBKeUTcjtb49GQYtXFtv7WyYdrovoFHE/viewform">Feedback</a></p>



<?php
//$sess_id = session_id();
//echo "<p>debug $sess_id</p>";

// connect to the database
$dbh = dbConnect();

// prepare queries
$q_user_exists = $dbh->prepare("SELECT COUNT(*) AS user_exists FROM Users WHERE name = :name OR email = :email");
$q_add_user = $dbh->prepare("INSERT INTO Users (name, password, email) VALUES (:name, MD5(:password), :email)");
$q_check_login = $dbh->prepare ("SELECT COUNT(*) AS login_ok FROM Users WHERE name = :name AND password = MD5(:password)");

// logout the user if that's what they asked for
if (isset($_POST['logout'])) {
	//echo '<p>debug A</p>';
	unset($uid);
	unset($pwd);
	unset($_SESSION['uid']);
	unset($_SESSION['pwd']);
}

// register the user if they have filled in the rego form
if (isset($_POST['rego'])) {
		//echo '<p>debug B</p>';
		// TODO: server side form validation
		// check uid is unique
		$q_user_exists->bindParam(':name', $_POST['new_uid']);
		$q_user_exists->bindParam(':email', $_POST['new_email']);
		$q_user_exists->execute();
		$user_exists = $q_user_exists->fetchColumn();
		if ($user_exists) {
			//echo '<p>debug C</p>';
			echo '<p>Sorry, that username or email address already exists in our system.</p>';
			include_once 'login_form.inc'; // display login or register form
		} else {
			//echo '<p>debug D: ' . $_POST['new_uid'] . '</p>';
			// add user to database
			$q_add_user->bindParam(':name', $_POST['new_uid']);
			$q_add_user->bindParam(':password', $_POST['new_pwd']);
			$q_add_user->bindParam(':email', $_POST['new_email']);
			$q_add_user->execute();
			
			// set up user directorys
			mkdir("./users/{$_POST['new_uid']}/images", 0777, true);
			
			$uid = $_POST['new_uid'];
			$pwd = $_POST['new_pwd'];
		}
		//echo '<p>debug E</p>';
	}
	//echo '<p>debug F</p>';
	
	
if (!isset($uid)) {
	//echo '<p>debug G</p>';
	// if the user is not logged in provide login or rego form
	include_once 'login_form.inc';
}

else { // the user is logged in or attempting to log in
	//echo '<p>debug H</p>';
	// check the user credentials
	$q_check_login->bindParam(':name', $uid);
	$q_check_login->bindParam(':password', $pwd);
	$q_check_login->execute();
	$login_ok = $q_check_login->fetchColumn();
	
	if (!$login_ok) { // failed login
		//echo '<p>debug I</p>';
		unset($uid);
		unset($pwd);
		echo '<p>Login Failed</p>';
		include_once 'login_form.inc';
	}
	
	else { // the rest of the app happens once the user is logged in
		//echo '<p>debug J</p>';
		$_SESSION['uid'] = $uid;
		$_SESSION['pwd'] = $pwd;
		//echo "<p>debug uid {$_SESSION['uid']}</p>";
		
		if (isset($_POST['ch_pwd'])) { //  user has submitted the change password form
			$q_ch_pwd = $dbh->prepare("UPDATE Users SET password=MD5(:password) WHERE name=:name");
			$q_ch_pwd->bindParam(':name', $uid);
			$q_ch_pwd->bindParam(':password', $_POST['ch_pwd']);
			$q_ch_pwd->execute();
			if ($q_ch_pwd->rowCount() > 0) {
				$pwd = $_POST['ch_pwd'];
				$_SESSION['pwd'] = $pwd;
				echo '<p>Password Updated.</p>';
			} else {
				echo '<p>Password Update Failed!</p>';
			}
		}
		
		$dbh = null;
 ?> 


<div class="ui-widget"> <!-- logout or change password -->

<div><?=$uid?></div>

<div>
<button class="reveal_trigger">Change Password</button>
<form class="hidden" id="chpwd_form" method="post" action="<?=$_SERVER['PHP_SELF']?>">
<div><label for="ch_pwd">New Password:</label>
<input id="ch_pwd" type="password" required="required" name="ch_pwd" /></div>
<div><label for="conf_ch_pwd">Confirm new password:</label>
<input id="conf_ch_pwd" type="password" required="required" name="conf_ch_pwd" /></div>
<div><input type="submit" class="button" value="Change Password" /></div>
</form></div>

<div><form method="post" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="logout" value="1" />
<input type="submit" class="button" value="logout" />
</form></div>

</div>

<div id="accordion">  
  <h3>Compose Newsletter</h3>
  <div>
  <!--fieldset><legend>Newsletter</legend-->
  
  <button class="saveIssue">Save this revision</button>
  Last save: <span class="last_save_date"></span>
  <select class="load_revision">
	  <option value="default" selected>Load previous revision</option>
  </select>
  <button class="clear left">Clear</button>
  
  <label for="template">Template: </label>
  <select name="template" id="template" class="input-issue save">
    <option value="cool.php" selected="selected">Cool</option>
  </select>
  
  <!-- This is tabs, one for the header of each format-->
  <div class="tabs" id="header">
	  <ul>
        <li><a href="#emailHeader"><span>Email</span></a></li>
        <li><a href="#webHeader"><span>Web</span></a></li>
        <li><a href="#printHeader"><span>Print</span></a></li>
      </ul>
	<div id="emailHeader">
		<textarea class="input-issue save" rows="8" cols="40"></textarea>
	</div>
	<div id="webHeader">
		<textarea class="input-issue save" rows="8" cols="40"></textarea>
	</div>
	<div id="printHeader">
		<textarea class="input-issue save" rows="8" cols="40"></textarea>
	</div>
</div>
  
  <div class="right" id="logo">
	<p>Organisation logo:</p>
	<button>Upload logo</button>
  </div>
  
  <div class="right" id="mugshot">
	<p>Your mugshot:</p>
	<button>Upload mugshot</button>
  </div>
  
  <div><label for="newsletterTitle">Newsletter title: </label>
  <input class="input-issue save" type="text" size="30" name="newsletterTitle" id="newsletterTitle"/></div>
  
  <div><label for="issuednum">Issue number: </label>
  <input class="input-issue save" type="text" name="issuenum" size="4" maxlength="3" id="issuenum" />
  <label for="issuedate">Issue date: </label>
  <input class="input-issue save" type="text" name="issuedate" size="12" id="issuedate" /></div>

<div id="newslettercontent">

<div id="leftPanel" class="section ui-corner-all">
  <!--<textarea class="remember" name="content" id="content" rows="20" cols="60"></textarea>-->
  <button class="addArticle">Add article</button>
</div>

<div id="rightPanel" class="section ui-corner-all">
  <button class="addArticle">Add article</button>
</div>

</div>


  <!-- This is tabs, one for the footer of each format-->
<div class="tabs" id="footer">
	  <ul>
        <li><a href="#emailFooter"><span>Email</span></a></li>
        <li><a href="#webFooter"><span>Web</span></a></li>
        <li><a href="#printFooter"><span>Print</span></a></li>
      </ul>
	<div id="emailFooter">
		<textarea class="input-issue save" rows="8" cols="40"></textarea>
	</div>
	<div id="webFooter">
		<textarea class="input-issue save" rows="8" cols="40"></textarea>
	</div>
	<div id="printFooter">
		<textarea class="input-issue save" rows="8" cols="40"></textarea>
	</div>
</div>

<button class="saveIssue right">Save this revision</button>
<button class="clear left">Clear</button>
  
<!--/fieldset-->

<div class="section">
	<fieldset><legend>Privacy</legend>
	My online content is
	<div id="privacy_radioset">
		<input type="radio" id="privacy_public" name="privacy" checked="checked" /><label for="privacy_public">Public</label>
		<input type="radio" id="privacy_protected" name="privacy" /><label for="privacy_protected">Password protected</label>
	</div>
	<div id="privacy_credentials" class="hidden">
		<label for="privacy_user">Username</label><input type="text" id="privacy_username" name="privacy_user" />
		<br />
		<label for="privacy_pass">Password</label><input type="password" id="privacy_password" name="privacy_pass" />
		<input type="checkbox" id="show_privacy_password" class="button" /><label for="show_privacy_password">Show</label>
	</div>
	<div id="privacy_msg"></div>
	</fieldset>
  <button id="generate" name="generate">Generate Newsletter</button>
  <div id="generateResults"></div>
</div>

</div>
<h3>Send Newsletter</h3>
<div>
  <fieldset><legend>Mail Recipients</legend>
  <!--<div><label for="generic_a">Generic messages:<br />
  <span class="small">Used in the place of personal messages for recipients that don't have them.</span></label>
  <textarea class="input-send save"  name="generic_a" id="generic_a" cols="30" rows="3"></textarea>
  <textarea class="input-send save"  name="generic_b" id="generic_b" cols="30" rows="3"></textarea></div>-->
  
  
  <div id="sent" class="right">
    <p id="sendMessage"></p>
    <table summary="recipients who have received the email">
      <tbody id="sentEmails">
      </tbody>
    </table>
  </div>
  
  <div id="sendTo">
  <table summary="Email recipients names and email addresses" >
	  <col width="20%" />
	  <col width="25%" />
	  <col width="25%" />
	  <col width="25%" />
	  <col width="5%" />
    <thead><tr>
		<td>Generic messages</td>
		<td><span class="small">Used in the place of personal messages for recipients that don't have them.</span></td>
		<td><textarea class="input-send save" name="generic_a" id="generic_a" cols="30" rows="3" placeholder="Generic message A"></textarea></td>
		<td><textarea class="input-send save" name="generic_b" id="generic_b" cols="30" rows="3" placeholder="Generic message B"></textarea></td>
		<td></td>
	</tr></thead>
    <thead><tr><td>Dear...</td><td>Email</td><td>Personal message A</td><td>Personal message B</td><td>Send results</td></tr></thead>
    <tbody id="all_recipients">
<?php
//Сheck that we have a file to import recipients from
if((!empty($_FILES["recipientsFile"])) && ($_FILES['recipientsFile']['error'] == 0)) {
	// ExcelFile($filename, $encoding);
   $data = new Spreadsheet_Excel_Reader();
   $data->setOutputEncoding('CP1251');
   $data->setRowColOffset(0);
   $data->read($_FILES['recipientsFile']['tmp_name']);
   for ($row = 0; $row < $data->sheets[0]['numRows']; ++$row)
   {
   	$email = htmlspecialchars($data->sheets[0]['cells'][$row][0]);
   	$name = htmlspecialchars($data->sheets[0]['cells'][$row][1]);
      if ($email == 'email') continue; // first row is headings
      if (!$name)
      { // if the dear field is not filled in use full name
        // if full name is more than one word chop off the last word
        $fullName = htmlspecialchars($data->sheets[0]['cells'][$row][2]);
        $space = strrpos($fullName, " ");
        if ($space === false) $name = $fullName;
        else $name = substr($fullName, 0, $space);
      }
   	echo "<tr class=\"recipient\"><td><input type=\"text\" class=\"name input-send save\" value=\"$name\" /></td>\n";
   	echo "<td><input type=\"text\" class=\"email input-send save\" value=\"$email\" /></td>\n";
   	echo "<td><button class=\"addGreeting\">Add personal greeting</button>";
   	echo "<textarea  class=\"greeting greetingA hidden\" rows=\"3\" cols=\"30\"></textarea></td>\n";
   	echo "<td><textarea  class=\"greeting greetingB hidden\" rows=\"3\" cols=\"30\"></textarea></td>\n";
   	echo "<td class=\"send_result\"></td>\n";
   	echo "<td class=\"controls\"><img class=\"delete\" src=\"images/delete.png\" /></td></tr>\n";
    }
}
?>
      <tr class="newRecipient"><td><input type="text" class="name" /></td>
      <td><input type="text" class="email" /></td>
      <td><button class="addGreeting">Add personal greeting</button><textarea class="greeting greetingA hidden" rows="3" cols="30" /></textarea></td>
      <td><textarea class="greeting greetingB hidden" rows="3" cols="30" /></textarea></td></tr>
    </tbody>
  </table>
  <div>
<form enctype="multipart/form-data" action="index.php" method="post">
    <label for="recipientsFile">Get recipients from an Excel file: </label>
    <input type="file" id="recipientsFile" name="recipientsFile" />
    <input type="submit" class="button" value="Import" />
  </form></div>
  </div>
  </fieldset>
  <fieldset><legend>Other Email Headers</legend>
  <div>
    <label for="from">From address: </label>
    <input  type="text" name="from" id="from" class="input-send save" />
    <label for="subject">Email subject line: </label>
    <input  type="text" name="subject" id="subject" class="input-send save" />
    <!--TODO: add a reply-to field -->
  </div>
  </fieldset>
  <fieldset><legend>Outgoing Mail Server</legend>
  <div>
    <label for="smtpProtocol">SMTP Protocol:</label>
    <select name="smtpProtocol" id="smtpProtocol" class="input-send save">
      <option value="ssl" selected="selected">ssl</option>
      <option value="tls">tls</option>
    </select>
    <!-- join like smtpProtocol + '://' + smtpHost -->
    <label for="smtpHost">SMTP host: <span class="small">e.g. mail.jaars.org</span></label>
    <input type="text" name="smtpHost" id="smtpHost" class="input-send save" />
    <label for="smtpPort"><span class="small">e.g. 465</span>SMTP port number: </label>
    <input type="text" name="smtpPort" id="smtpPort" class="input-send save" /></div>
    <div><label for="smtpUser">Mail username: </label>
    <input type="text" name="smtpUser" id="smtpUser" class="input-send save" />
    <label for="SMTPpass">Mail password: </label>
    <input type="password" name="smtpPass" id="smtpPass" class="input-send save" /></div>
    <div><input type="hidden" id="newsletter_file_name" />
    <button id="send" name="send">Send</button>
  </div>
  
  </fieldset>
  
<?php 
	// end the else block for what you get once the user is logged in
	}
}
?>
</div> <!-- end of send newsletter slice -->
</div> <!-- end of accordian -->
</body>
</html>
