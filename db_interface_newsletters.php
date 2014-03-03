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
	
	$current_newsletter_id  = null;
	if (isset($_POST['newsletter_id']))
	{
		$current_newsletter_id = $_POST['newsletter_id'];
		//TODO: check this newsletter belongs to this user
	}
	
	function getCurrentNewsletterID()
	{
		// if we don't know which is the current newsletter assume it's the most recent
		if ($current_newsletter_id == null)
		{
			$q_recent_newsletter_id = $dbh->query("SELECT id FROM Newsletters1 Where user={$db_uid} HAVING MAX(`timestamp`)");
			$recent_newsletter_id = $q_recent_newsletter_id->fetchAll(PDO:FETCH_ASSOC);
			$current_newsletter_id = $recent_newsletter_id[0]['id'];
			$recent_newsletter_id->closeCursor();
		}
		return $current_newsletter_id;
	}
	
	if (strcmp($_POST['task'], 'save') == 0)
	{		
		// create a new entry in the NewsletterSaves table
		$q_save_newsletter = $dbh->prepare("INSERT INTO NewsletterSaves (newsletter, content) VALUES (:newsletter_id,:content)");
		$q_save_newsletter->bindParam(':newsletter_id', getCurrentNewsletterID());
		$q_save_newsletter->bindParam(':content', json_encode($_POST['content']));
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
	}
	
	else if (strcmp($_POST['task'], 'autosave') == 0)
	{
		// this is to happen after every change of the newsletter to keep the history table up to date
		// first copy the current content into the history table
		$dbh->query("INSERT INTO NewsletterHistory (newsletter, content) SELECT id, content FROM Newsletters1 WHERE Newsletters1.id = " . getCurrentNewsletterID());
		// now update the content in the current newsletter
		$q_save_newsletter = $dbh->prepare("UPDATE Newsletters1 SET content=:content WHERE id=:id");
		$q_save_newsletter->bindParam(':content', json_encode($_POST['content']));
		$q_save_newsletter->bindParam(':id', getCurrentNewsletterID());
		$q_save_newsletter->execute();
		$save_affected = $q_save_newsletter->rowCount();
		if ($save_affected == 1)
		{
			//echo 'Newsletter saved';
			// return the datetime of the successful save in UTC
			echo gmdate('Y-m-d H:i:s'), ' UTC';
			
			// Finally empty the future table for this user because we can't redo from here
			$dbh->query("DELETE FROM NewsletterFuture WHERE newsletter IN (SELECT id FROM Newsletters1 WHERE user = " . getCurrentNewsletterID() . ')');
		}
		else
		{
			echo "Save Failed: $save_affected records made/modified (expecting 1)";
		}
	}
	
	else if (strcmp($_POST['task'], 'get_all_saves') == 0)
	{
		// this will get a list of ids and dates for all saved instances of a given newsletter issue
		$q_get_revisions = $dbh->prepare("SELECT id,timestamp FROM NewsletterSaves WHERE newsletter=:newsletter_id ORDER BY timestamp DESC");
		$q_get_revisions->bindParam(':newsletter_id', getCurrentNewsletterID);
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
		$q_get_newsletter = $dbh->prepare("SELECT * FROM Newsletters1 WHERE id=:id");
		$q_get_newsletter->bindParam(':id', getCurrentNewsletterID());
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
	
	else if (strcmp($_POST['task'], 'undo') == 0)
	{
		// get the latest entry for the current newsletter from the history table
		$q_latest_history = $dbh->query("SELECT * FROM NewsletterHistory WHERE newsletter = " . getCurrentNewsletterID() . " HAVING MAX(`timestamp`)");
		if ($q_latest_history->rowCount() == 1)
		{
			// store the current content into the future table
			$dbh->query("INSERT INTO NewsletterFuture (newsletter, content) SELECT id, content FROM Newsletters1 WHERE Newsletters1.id = " . getCurrentNewsletterID());
			// update the newsletter table with the data from history
			$latest_history = $q_latest_history->fetchAll(PDO::FETCH_ASSOC);
			$q_save_newsletter = $dbh->prepare("UPDATE Newsletters1 SET content=:content WHERE id=:id");
			$q_save_newsletter->bindParam(':content', $latest_history[0]['content']);
			$q_save_newsletter->bindParam(':id', getCurrentNewsletterID());
			$q_save_newsletter->execute();
			// delete the record we used from the history table
			$dbh->query("DELETE FROM NewslettersHistory WHERE id = " . $latest_history[0]['id']);
		}
		else
		{
			// do nothing if there is no data to load from db
			// The js that calls this file will get back an empty string
		}
	}
	
	else if (strcmp($_POST['task'], 'redo') == 0)
	{
		// get the latest entry for the current newsletter from the future table
		$q_latest_future = $dbh->query("SELECT * FROM NewsletterFuture WHERE newsletter = " . getCurrentNewsletterID() . " HAVING MAX(`timestamp`)");
		if ($q_latest_future->rowCount() == 1)
		{
			// store the current content into the history table
			$dbh->query("INSERT INTO NewsletterHistory (newsletter, content) SELECT id, content FROM Newsletters1 WHERE Newsletters1.id = " . getCurrentNewsletterID());
			// update the newsletter table with the data from future
			$latest_future = $q_latest_future->fetchAll(PDO::FETCH_ASSOC);
			$q_save_newsletter = $dbh->prepare("UPDATE Newsletters1 SET content=:content WHERE id=:id");
			$q_save_newsletter->bindParam(':content', $latest_future[0]['content']);
			$q_save_newsletter->bindParam(':id', getCurrentNewsletterID());
			$q_save_newsletter->execute();
			// delete the record we used from the future table
			$dbh->query("DELETE FROM NewslettersFuture WHERE id = " . $latest_future[0]['id']);
		}
		else
		{
			// do nothing if there is no data to load from db
			// The js that calls this file will get back an empty string
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
