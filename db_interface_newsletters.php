<?php

require_once 'db.php';
require_once 'common.php';


// check user is logged in
if (login_ok() == 1) {
	
	//echo "UID: {$_SESSION['uid']} ";
	//echo "title: {$_POST['title']} ";
	
	$dbh = dbConnect();
	
	// get db record id for this user by querying using the PHP session uid
	$q_get_uid = $dbh->prepare("SELECT id FROM Users WHERE name=:user_name");
	$q_get_uid->bindParam(':user_name', $_SESSION['uid']);
	$q_get_uid->execute();
	$db_uid_row = $q_get_uid->fetch();
	$db_uid = $db_uid_row['id'];
	
	// set up some statements for clearing the current_newsletter and current_revision flags for this user
	$q_clear_current_newsletter = $dbh->prepare("UPDATE Newsletters SET current_newsletter=0 WHERE user=:user");
	$q_clear_current_newsletter->bindParam(':user', $db_uid);
	
	/*
	$q_clear_current_revison = $dbh->prepare("UPDATE Newsletters SET current_revision=0 WHERE user=:user AND name=':newsletter_name' AND issue=':issue'");
	$q_clear_current_revison->bindParam(':user', $_SESSION['uid']);
	$q_clear_current_revison->bindParam(':newsletter_name', $_POST['title']);
	$q_clear_current_revison->bindParam(':issue', $_POST['issue']);
	*/
	
	// get the record of the current save for this newsletter
	$q_get_current = $dbh->prepare("SELECT * FROM Newsletters WHERE user=:user AND name=:newsletter_name AND issue=:issue AND current_revision=1");
	$q_get_current->bindParam(':user', $db_uid);
	$q_get_current->bindParam(':newsletter_name', $_POST['title']);
	$q_get_current->bindParam(':issue', $_POST['issue']);
	$q_get_current->execute();
	$current_revision = $q_get_current->fetchAll(PDO::FETCH_ASSOC);
	
	if (sizeof($current_revision) > 1)
	{
		// if there's more than on current revision of this newsletter something went seriously wrong
		//TODO: make the one with the most recent timestamp the current
	}
	
	if (strcmp($_POST['task'], 'save') == 0)
	{
		
		if (sizeof($current_revision) > 0)
		{
			echo 'existing save found ';
			// first clear the current newsletter flag for this user
			$q_clear_current_newsletter->execute();
			
			// we want to overwrite the exising current revison making it also the current newsletter
			$q_save_newsletter = $dbh->prepare("UPDATE Newsletters SET content=:content, current_newsletter=1 WHERE id=:id");
			$q_save_newsletter->bindParam(':content', json_encode($_POST['content']));
			$q_save_newsletter->bindParam(':id', $current_revision[0]['id']);
		}
		
		else
		{
			// there is no current revision so this must be the first save for this newsletter
			// first clear the current newsletter flag
			// so create a new entry
			$q_save_newsletter = $dbh->prepare("INSERT INTO Newsletters (name,issue,content,current_revision,current_newsletter,user) VALUES (:newsletter_name,:issue,:content,1,1,:user)");
			$q_save_newsletter->bindParam(':newsletter_name', $_POST['title']);
			$q_save_newsletter->bindParam(':issue', $_POST['issue']);
			$q_save_newsletter->bindParam(':content', json_encode($_POST['content']));
			$q_save_newsletter->bindParam(':user', $db_uid);
		}
		
		//$q_save_newsletter->debugDumpParams();
		$q_save_newsletter->execute();
		$save_affected = $q_save_newsletter->rowCount();
		if ($save_affected == 1)
		{
			echo 'Newsletter saved';
		}
		else
		{
			echo "Save Failed: $save_affected records made/modified (expecting 1)";
		}
	}
	
	else if (strcmp($_POST['task'], 'save_instance') == 0)
	{
		// this will save a copy of the newsletter that is not current. It will insert a new record into the database that will not be overwritten
		// an instance can be restored later by the user
		$q_save_newsletter = $dbh->prepare("INSERT INTO Newsletters (name,issue,content,current_revision,current_newsletter,user) VALUES (:newsletter_name,:issue,:content,0,0,:user)");
		$q_save_newsletter->bindParam(':newsletter_name', $_POST['title']);
		$q_save_newsletter->bindParam(':issue', $_POST['issue']);
		$q_save_newsletter->bindParam(':content', json_encode($_POST['content']));
		$q_save_newsletter->bindParam(':user', $db_uid);
		$q_save_newsletter->execute();
		$save_affected = $q_save_newsletter->rowCount();
		if ($save_affected == 1)
		{
			echo 'Newsletter saved';
		}
		else
		{
			echo "Save Failed: $save_affected records made/modified (expecting 1)";
		}
	}
	
	else if (strcmp($_POST['task'], 'restore') == 0)
	{
		// get the saved record of the current newsletter
		$q_get_current_newsletter = $dbh->prepare("SELECT * FROM Newsletters WHERE user=:user AND current_newsletter=1");
		$q_get_current_newsletter->bindParam(':user', $db_uid);
		$q_get_current_newsletter->execute();
		$current_newsletter = $q_get_current_newsletter->fetchAll(PDO::FETCH_ASSOC);
		
		if (sizeof($current_newsletter) > 0)
		{
			echo $current_newsletter[0]['content'];
		}
		else
		{
			// do nothing if there is no data to load from db
			// The js that calls this file will get back an empty string
		}
	}
	
	else
	{
		echo "<p>Fail. unrecognised task: {$_POST['task']}</p>";
	}
	
	$dbh = null;
	
} else {
	echo '<p>Fail. Could not verify user. Login again.</p>';
}
