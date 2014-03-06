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
	
	$current_newsletter_id  = null;
	if (isset($_POST['newsletter_id']))
	{
		// if a newsletter id is fed in we have to check it exists and belongs to the current user
		$q_check_id = $dbh->prepare("SELECT * FROM Newsletters1 WHERE id = :id and user = :user");
		$q_check_id->bindParam(':id', $_POST['newsletter_id']);
		$q_check_id->bindParam(':user', $db_uid);
		$q_check_id->execute();
		if ($q_check_id->rowCount() > 0)
		{
			$current_newsletter_id = $_POST['newsletter_id'];
		}
	}
	
	function getCurrentNewsletterID($dbh, $db_uid)
	{
		// if we don't know which is the current newsletter assume it's the most recent
		if ($current_newsletter_id == null)
		{
			$q_recent_newsletter_id = $dbh->query("SELECT id FROM Newsletters1 WHERE user = " . $db_uid . " HAVING MAX(`timestamp`)");
			$recent_newsletter_id = $q_recent_newsletter_id->fetchAll(PDO::FETCH_ASSOC);
			$current_newsletter_id = $recent_newsletter_id[0]['id'];
			$q_recent_newsletter_id->closeCursor();
		}
		return $current_newsletter_id;
	}
	
	if (strcmp($_POST['task'], 'get_newsletter_id') == 0)
	{
		echo getCurrentNewsletterID($dbh, $db_uid);
	}
	
	else if (strcmp($_POST['task'], 'save') == 0)
	{		
		// create a new entry in the NewsletterSaves table
		$q_save_newsletter = $dbh->prepare("INSERT INTO NewsletterSaves (newsletter, content) VALUES (:newsletter_id,:content)");
		$q_save_newsletter->bindParam(':newsletter_id', getCurrentNewsletterID($dbh, $db_uid));
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
		$dbh->query("INSERT INTO NewsletterHistory (newsletter, content) SELECT id, content FROM Newsletters1 WHERE Newsletters1.id = " . getCurrentNewsletterID($dbh, $db_uid));
		// now update the content in the current newsletter
		$q_save_newsletter = $dbh->prepare("UPDATE Newsletters1 SET content=:content WHERE id=:id");
		$q_save_newsletter->bindParam(':content', json_encode($_POST['content']));
		$q_save_newsletter->bindParam(':id', getCurrentNewsletterID($dbh, $db_uid));
		$q_save_newsletter->execute();
		$save_affected = $q_save_newsletter->rowCount();
		if ($save_affected == 1)
		{
			//echo 'Newsletter saved';
			// return the datetime of the successful save in UTC
			echo gmdate('Y-m-d H:i:s'), ' UTC';
			
			// Finally delete entries for this newsletter from the future table for this user because we can't redo from here
			$dbh->query("DELETE FROM NewsletterFuture WHERE newsletter = " . getCurrentNewsletterID($dbh, $db_uid) . ')');
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
		$q_get_revisions->bindParam(':newsletter_id', getCurrentNewsletterID($dbh, $db_uid));
		$q_get_revisions->execute();
		if ($q_get_revisions->rowCount() > 0)
		{
			$revisions = $q_get_revisions->fetchAll(PDO::FETCH_ASSOC);
			// convert all the dates to UTC
			foreach ($revisions as $rev_i => $revision)
			{
				$revisions[$rev_i]['timestamp'] = gmdate('Y-m-d H:i:s', date_timestamp_get(date_create($revisions[$rev_i]['timestamp']))) . ' UTC';
			}
			echo json_encode($revisions);
		}
		else
		{
			// do nothing if there is no data to load from db
			// The js that calls this file will get back an empty string
		}
	}
	
	else if (strcmp($_POST['task'], 'restore') == 0)
	{
		// get the saved record of the current newsletter
		$q_get_newsletter = $dbh->prepare("SELECT * FROM Newsletters1 WHERE id=:id");
		$q_get_newsletter->bindParam(':id', getCurrentNewsletterID($dbh, $db_uid));
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
		$q_latest_history = $dbh->query("SELECT * FROM NewsletterHistory WHERE newsletter = " . getCurrentNewsletterID($dbh, $db_uid) . " HAVING MAX(`timestamp`)");
		if ($q_latest_history->rowCount() == 1)
		{
			// store the current content into the future table
			$dbh->query("INSERT INTO NewsletterFuture (newsletter, content) SELECT id, content FROM Newsletters1 WHERE Newsletters1.id = " . getCurrentNewsletterID($dbh, $db_uid));
			// update the newsletter table with the data from history
			$latest_history = $q_latest_history->fetchAll(PDO::FETCH_ASSOC);
			$q_save_newsletter = $dbh->prepare("UPDATE Newsletters1 SET content=:content WHERE id=:id");
			$q_save_newsletter->bindParam(':content', $latest_history[0]['content']);
			$q_save_newsletter->bindParam(':id', getCurrentNewsletterID($dbh, $db_uid));
			$q_save_newsletter->execute();
			// delete the record we used from the history table
			$dbh->query("DELETE FROM NewslettersHistory WHERE id = " . $latest_history[0]['id']);
		}
		else
		{
			echo 'no data';
		}
	}
	
	else if (strcmp($_POST['task'], 'redo') == 0)
	{
		// get the latest entry for the current newsletter from the future table
		$q_latest_future = $dbh->query("SELECT * FROM NewsletterFuture WHERE newsletter = " . getCurrentNewsletterID($dbh, $db_uid) . " HAVING MAX(`timestamp`)");
		if ($q_latest_future->rowCount() == 1)
		{
			// store the current content into the history table
			$dbh->query("INSERT INTO NewsletterHistory (newsletter, content) SELECT id, content FROM Newsletters1 WHERE Newsletters1.id = " . getCurrentNewsletterID($dbh, $db_uid));
			// update the newsletter table with the data from future
			$latest_future = $q_latest_future->fetchAll(PDO::FETCH_ASSOC);
			$q_save_newsletter = $dbh->prepare("UPDATE Newsletters1 SET content=:content WHERE id=:id");
			$q_save_newsletter->bindParam(':content', $latest_future[0]['content']);
			$q_save_newsletter->bindParam(':id', getCurrentNewsletterID($dbh, $db_uid));
			$q_save_newsletter->execute();
			// delete the record we used from the future table
			$dbh->query("DELETE FROM NewslettersFuture WHERE id = " . $latest_future[0]['id']);
		}
		else
		{
			echo 'no data';
		}
	}
	
	else if (strcmp($_POST['task'], 'change_newsletter') == 0)
	{
		$q_find_newsletter = $dbh->prepare("SELECT id FROM Newsletters1 WHERE user=:user AND name=:title AND issue=:issue");
		$q_find_newsletter->bindParam(':user', $db_uid);
		$q_find_newsletter->bindParam(':title', $_POST['newsletter_title']);
		$q_find_newsletter->bindParam(':issue', $_POST['newsletter_issue']);
		$q_find_newsletter->execute();
		// if the newsletter doesn't exist add it
		if ($q_find_newsletter->rowCount() == 0)
		{
			$q_new_newsletter = $dbh->prepare("INSERT INTO Newsletters1 (user, name, issue, content) VALUES (:user, :title, :issue, :content)");
			$q_new_newsletter->bindParam(':user', $db_uid);
			$q_new_newsletter->bindParam(':title', $_POST['newsletter_title']);
			$q_new_newsletter->bindParam(':issue', $_POST['newsletter_issue']);
			$q_new_newsletter->bindParam(':content', json_encode($_POST['content']));
			$q_new_newsletter->execute();
			// now we can try that original query again.
			$q_find_newsletter->execute();
		}
		
		$found_newsletter = $q_find_newsletter->fetchAll(PDO::FETCH_ASSOC);
		echo $found_newsletter[0]['id'];
		
	}
	
	
	else if (strcmp($_POST['task'], 'clear_old_history') == 0)
	{
		// clear both the redo and undo history of entries older than a day
		$yesterday = strtotime('-1 day');
		$table = 'NewsletterHistory';
		$q_clear_table = $dbh->prepare("DELETE FROM :table WHERE timestamp<:expire AND newsletter IN (SELECT id FROM Newsletters1 WHERE user = " . getCurrentNewsletterID($dbh, $db_uid) . ')');
		$q_clear_table->bindParam(':expire', $yesterday);
		$q_clear_table->bindParam(':table', $table);
		$q_clear_table->execute();
		$table = 'NewsletterFuture';
		$q_clear_table->execute();
	}
	
	else
	{
		echo "<p>Fail. unrecognised task: {$_POST['task']}</p>";
	}
	
	$dbh = null;
	
} else {
	echo '<p>Fail. Could not verify user. Login again.</p>';
}
