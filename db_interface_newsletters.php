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
	
	
	$q_clear_current_revison = $dbh->prepare("UPDATE Newsletters SET current_revision=0 WHERE user=:user AND name=':newsletter_name' AND issue=':issue'");
	$q_clear_current_revison->bindParam(':user', $_SESSION['uid']);
	$q_clear_current_revison->bindParam(':newsletter_name', $_POST['title']);
	$q_clear_current_revison->bindParam(':issue', $_POST['issue']);
	
	
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
		// first clear the current newsletter and revision flags
		$q_clear_current_newsletter->execute();
		$q_clear_current_revision->execute();
		
		// now create a new entry
		$q_save_newsletter = $dbh->prepare("INSERT INTO Newsletters (name,issue,content,current_revision,current_newsletter,user, previous) VALUES (:newsletter_name,:issue,:content,1,1,:user, :previous)");
		$q_save_newsletter->bindParam(':newsletter_name', $_POST['title']);
		$q_save_newsletter->bindParam(':issue', $_POST['issue']);
		$q_save_newsletter->bindParam(':content', json_encode($_POST['content']));
		$q_save_newsletter->bindParam(':user', $db_uid);
		$q_save_newsletter->bindParam(':previous', $current_revision[0]['id'])	;
		//$q_save_newsletter->debugDumpParams();
		$q_save_newsletter->execute();
		$save_affected = $q_save_newsletter->rowCount();
		if ($save_affected == 1)
		{
			// return the datetime of the successful save in UTC
			echo gmdate('Y-m-d H:i:s'), ' UTC';
		}
		else
		{
			echo "Save Failed: $save_affected records made/modified (expecting 1)";
		}
		
		// also set the next field of the previously current record to refer to the currently current record
		$q_set_next = $dbh->prepare("UPDATE Newsletters SET next=(SELECT id FROM Newsletters WHERE current_revision=1 AND current_newsletter=1) WHERE id=:x_current_id");
		$q_set_next->bindParam(':x_current_id', $current_revision[0]['id']);
		$q_set_next->execute();
	}
	
	else if (strcmp($_POST['task'], 'save_instance') == 0)
	{
		// this will save a copy of the newsletter that is not current. It will insert a new record into the database that will not be overwritten
		// an instance can be restored later by the user
		$q_save_newsletter = $dbh->prepare("INSERT INTO Newsletters (name,issue,content,permanent,current_revision,current_newsletter,user) VALUES (:newsletter_name,:issue,:content,1,0,0,:user)");
		$q_save_newsletter->bindParam(':newsletter_name', $_POST['title']);
		$q_save_newsletter->bindParam(':issue', $_POST['issue']);
		$q_save_newsletter->bindParam(':content', json_encode($_POST['content']));
		$q_save_newsletter->bindParam(':user', $db_uid);
		$q_save_newsletter->execute();
		$save_affected = $q_save_newsletter->rowCount();
		if ($save_affected == 1)
		{
			//echo 'Newsletter saved';
			// return the datetime of the successful save in UTC
			echo gmdate('Y-m-d H:i:s'), ' UTC';
		}
		else
		{
			echo "Save Failed: $save_affected records made/modified (expecting 1)";
		}
	}
	
	else if (strcmp($_POST['task'], 'get_all_instances') == 0)
	{
		// this will get a list of ids and dates for all saved instances for a given newsletter issue exluding the current one
		$q_get_revisions = $dbh->prepare("SELECT id,timestamp FROM Newsletters WHERE user=:user AND name=:newsletter_name AND issue=:issue AND current_revision=0 AND permanent=1 ORDER BY timestamp DESC");
		$q_get_revisions->bindParam(':user', $db_uid);
		$q_get_revisions->bindParam(':newsletter_name', $_POST['title']);
		$q_get_revisions->bindParam(':issue', $_POST['issue']);
		$q_get_revisions->execute();
		$revisions = $q_get_revisions->fetchAll(PDO::FETCH_ASSOC);
		// convert all the dates to UTC
		foreach ($revisions as $rev_i => $revision)
		{
			$revisions[$rev_i]['timestamp'] = gmdate('Y-m-d H:i:s', date_timestamp_get(date_create($revisions[$rev_i]['timestamp']))) . ' UTC';
		}
		echo json_encode($revisions);
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
	
	else if (strcmp($_POST['task'], 'restore_from_id') == 0)
	{
		// get the saved record of the identified newsletter
		// also match the user id in the query to make certain one user can't restore another user's newsletter
		$q_get_newsletter = $dbh->prepare("SELECT * FROM Newsletters WHERE user=:user AND id=:id");
		$q_get_newsletter->bindParam(':user', $db_uid);
		$q_get_newsletter->bindParam(':id', $_POST['newsletter_id']);
		$q_get_newsletter->execute();
		$newsletter = $q_get_newsletter->fetchAll(PDO::FETCH_ASSOC);
		
		if (sizeof($newsletter) > 0)
		{		
			echo $newsletter[0]['content'];
		}
		else
		{
			// do nothing if there is no data to load from db
			// The js that calls this file will get back an empty string
		}
	}
	
	else if (strcmp($_POST['task'], 'previous') == 0)
	{
		if ($current_revision[0]['previous'] != Null)
		{
			// return the previous to current instance
			$q_previous = $dbh->prepare("SELECT * FROM Newsletters WHERE user=:user AND id=:prev_id");
			$q_previous->bindParam(':user', $db_uid);
			$q_previous->bindParam(':prev_id', $current_revision[0]['previous']);
			$q_previous->execute();
			$newsletter = $q_previous->fetchAll(PDO::FETCH_ASSOC);
			if (sizeof($newsletter) > 0)
			{		
				echo $newsletter[0]['content'];
			}
			else
			{
				// do nothing if there is no data to load from db
				// The js that calls this file will get back an empty string
			}
		}
	}
	
	else if (strcmp($_POST['task'], 'next') == 0)
	{
		if ($current_revision[0]['next'] != Null)
		{
			// return the next to current instance
			$q_next = $dbh->prepare("SELECT * FROM Newsletters WHERE user=:user AND id=:next_id");
			$q_next->bindParam(':user', $db_uid);
			$q_next->bindParam(':next_id', $current_revision[0]['next']);
			$q_next->execute();
			$newsletter = $q_next->fetchAll(PDO::FETCH_ASSOC);
			if (sizeof($newsletter) > 0)
			{		
				echo $newsletter[0]['content'];
			}
			else
			{
				// do nothing if there is no data to load from db
				// The js that calls this file will get back an empty string
			}
		}
	}
	
	
	else if (strcmp($_POST['task'], 'clear_old') == 0)
	{
		// delete instances that are not permanent or current that are older than one day
		// it's okay to be deleting the data of all users here:
		// any non-permanent newsletter instance that has expired is due to go.
		$q_clear_old = $dbh->prepare("DELETE FROM Newsletters WHERE permanent=0 AND current_revision=0 AND timestamp<:expire");
		$yesterday = strtotime('-1 day');
		$q_clear_old->bindParam(':expire', $yesterday);
		$q_clear_old->execute();
	}
	
	else
	{
		echo "<p>Fail. unrecognised task: {$_POST['task']}</p>";
	}
	
	$dbh = null;
	
} else {
	echo '<p>Fail. Could not verify user. Login again.</p>';
}
