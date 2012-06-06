<?php
require_once 'Excel/reader.php';
require_once '../php/db.php';

?>
<!DOCTYPE html>

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

<link href="css/newsletterComposer.css?<?php echo time(); ?>" rel="stylesheet" type="text/css"/>

<?php

session_save_path(realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../sessions'));
session_start();

// get the user credentials from either the existing session or the login post
$uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : $_POST['uid'];
$pwd = isset($_SESSION['pwd']) ? $_SESSION['pwd'] : $_POST['pwd'];

if (isset($uid)) {
	echo "<script type=\"text/javascript\">var userName = '{$_SESSION['uid']}';</script>";
}

?>

<script src="js/json2.js" type="text/javascript"></script>
<script src="js/jquery-1.6.2.min.js" type="text/javascript"></script>
<script src="js/jquery.cookies.2.2.0.js" type="text/javascript"></script>
<script src="js/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
<script src="js/newsletterComposer.js?<?php echo time(); ?>" type="text/javascript"></script>

</head>


<body>

<h1>Newsletter Composer</h1>

<p><a href="instructions.html" >Instructions</a></p>



<?php
//$sess_id = session_id();
//echo "<p>debug $sess_id</p>";

// connect to the database
$dbh = dbConnect();

// prepare queries
$q_user_exists = $dbh->prepare("SELECT COUNT(*) AS user_exists FROM Users WHERE name = :name OR email = :email");
$q_add_user = $dbh->prepare("INSERT INTO Users (name, password, email) VALUES (:name, MD5(:password), :email)");
$q_check_login = $dbh->prepare ("SELECT COUNT(*) AS login_ok FROM Users WHERE name = :name AND password = MD5(:password)");

if (isset($_POST['logout'])) {
	//echo '<p>debug A</p>';
	unset($uid);
	unset($pwd);
	unset($_SESSION['uid']);
	unset($_SESSION['pwd']);
}
if (isset($_POST['rego'])) { // register the user
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
	echo '<p>debug G</p>';
	// if the user is not logged in provide login or rego form
	include_once 'login_form.inc';
} else {
	//echo '<p>debug H</p>';
	// check the user credentials
	$q_check_login->bindParam(':name', $uid);
	$q_check_login->bindParam(':password', $pwd);
	$q_check_login->execute();
	$login_ok = $q_check_login->fetchColumn();
	if (!$login_ok) {
		//echo '<p>debug I</p>';
		unset($uid);
		unset($pwd);
		echo '<p>Login Failed</p>';
		include_once 'login_form.inc';
	} else { // the rest of the app happens once the user is logged in
		echo '<p>debug J</p>';
		$_SESSION['uid'] = $uid;
		$_SESSION['pwd'] = $pwd;
		echo "<p>debug uid {$_SESSION['uid']}</p>";
 ?> 
<!--<div id="loading_splash">Please Wait ...</div>-->

<div class="right"> <!-- logout button -->
<p><?=$uid?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="logout" value="1" />
<input type="submit" value="logout" />
</form></p></div>

  <fieldset><legend>Personal details</legend>
  <div><label for="address_line_1">Address line 1: </label>
  <input class="input-personal save" type="text" size="30" name="address_line_1" id="address_line_1"/>
  <label for="address_line_2">Address line 2: </label>
  <input class="input-personal save" type="text" size="30" name="address_line_2" id="address_line_2"/></div>
  <div><label for="phone">Phone number(s): </label>
  <input class="input-personal save" type="text" size="30" name="phone" id="phone"/>
  <label for="skype">Skype address(es): </label>
  <input class="input-personal save" type="text" size="30" name="skype" id="skype"/></div>
  <div><label for="personal_web">Personal Website: </label>
  <input class="input-personal save" type="text" size="30" name="personal_web" id="personal_web"/></div>
  <div><label for="org_name">Organisation Name: </label>
  <input class="input-personal save" type="text" size="30" name="org_name" id="org_name"/>
  <label for="org_web">Organisation Website: </label>
  <input class="input-personal save" type="text" size="30" name="org_web" id="org_web"/></div>
  </fieldset>
  
  <fieldset><legend>Newsletter</legend>
  
  <button class="saveIssue right">Save to file</button>
  <div class="right sneaky-file-input"><button>Load from file</button><input type="file" class="loadIssue" /></div>
  <button class="clear left">Clear</button>
  
  <label for="template">Template: </label>
  <select name="template" id="template" class="input-issue save">
    <option value="cool.php" selected="selected">Cool</option>
  </select>
  
  <div><label for="newsletterTitle">Newsletter title: </label>
  <input class="input-issue save" type="text" size="30" name="newsletterTitle" id="newsletterTitle"/></div>
  <div><label for="subscribeURI">Subscribe address: <br />
  <span class="small">A person wishing to subscribe to the newsletter should go here.</span></label>
  <input class="input-issue save" type="text" size="30" name="subscribeURI" id="subscribeURI"/></div>
  <div><label for="unsubscribeURI">Unsubscribe address: <br />
  <span class="small">A person wishing to unsubscribe from the newsletter should go here.</span></label>
  <input class="input-issue save" type="text" size="30" name="unsubscribeURI" id="unsubscribeURI"/></div>
  
  <div><label for="issuednum">Issue number: </label>
  <input class="input-issue save" type="text" name="issuenum" size="4" maxlength="3" id="issuenum" />
  <label for="issuedate">Issue date: </label>
  <input class="input-issue save" type="text" name="issuedate" size="12" id="issuedate" /></div>

<div id="newslettercontent">

<div id="rightPanel" class="right">
  <button class="addArticle">Add article</button>
</div>

<div id="leftPanel">
  <!--<textarea class="remember" name="content" id="content" rows="20" cols="60"></textarea>-->
  <button class="addArticle">Add article</button>
</div>

</div>

<button class="saveIssue right">Save to file</button>
<button class="clear left">Clear</button>
  
</fieldset>

<fieldset><legend>Generated Newsletter</legend>
  <button id="generate" name="generate">Generate Newsletter</button>
  <div id="generateResults"></div>
</fieldset>

  <fieldset><legend>Send out newsletter</legend>
  <div><label for="greeting">Generic greeting:<br />
  <span class="small">will appear below "dear recipient" in the head of the email</span></label>
  <input class="input-send save" type="text" name="greeting" id="greeting" size="50" /></div>
  
  
  <div id="sent" class="right">
    <p id="sendMessage"></p>
    <table summary="recipients who have received the email">
      <tbody id="sentEmails">
      </tbody>
    </table>
  </div>
  
  <div id="sendTo">
  <table summary="Email recipients names and email addresses" >
    <thead><tr><td>Dear...</td><td>email</td><td>personal greeting</td></tr></thead>
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
   	echo "<tr class=\"recipient\"><td><input type=\"text\" class=\"name\" value=\"$name\" /></td>\n";
   	echo "<td><input type=\"text\" class=\"email\" value=\"$email\" /></td>\n";
   	echo "<td><button class=\"addGreeting\">Add personal greeting</button>";
   	echo "<input type=\"text\" class=\"greeting\" /></td>\n";
   	echo "<td class=\"controls\"><img class=\"delete\" src=\"images/delete.png\" /></td></tr>\n";
    }
}
?>
      <tr class="newRecipient"><td><input type="text" class="name" /></td>
      <td><input type="text" class="email" /></td>
      <td><button class="addGreeting">Add personal greeting</button><input type="text" class="greeting" /></td>
      </td></tr>
    </tbody>
  </table>
  <div>
<form enctype="multipart/form-data" action="composer.php" method="post">
    <label for="recipientsFile">Get recipients from an Excel file: </label>
    <input type="file" id="recipientsFile" name="recipientsFile" />
    <input type="submit" value="Go" />
  </form></div>
  </div>
  <div>
    <label for="from">From address: </label>
    <input  type="text" name="from" id="from" class="input-send save" />
    <label for="subject">Email subject line: </label>
    <input  type="text" name="subject" id="subject" class="input-send save" />
  </div>
  <div>
    <label for="smtpHost">SMTP host: <span class="small">e.g. ssl://mail.jaars.org</span></label>
    <input type="text" name="smtpHost" id="smtpHost" class="input-send save" />
    <label for="smtpPort"><span class="small">e.g. 465</span>SMTP port number: </label>
    <input type="text" name="smtpPort" id="smtpPort" class="input-send save" /></div>
    <div><label for="smtpUser">Mail username: </label>
    <input type="text" name="smtpUser" id="smtpUser" class="input-send save" />
    <label for="SMTPpass">Mail password: </label>
    <input type="password" name="smtpPass" id="smtpPass" class="input-send save" /></div>
    <div><input type="hidden" id="newsletter_file_name" />
    <button id="send" name="send" disabled="disabled">Send</button>
  </div>
  
  </fieldset>
  
<?php 
	// end the else block for what you get once the user is logged in
	}
}
?>

</body>
</html>
