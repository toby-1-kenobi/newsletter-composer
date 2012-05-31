<?php
require_once 'common.php';
// check user is logged in
if (login_ok() == 1) {
	//echo '<p>login ok</p>';
	
	// get the path to the images directory
	$rel_user_path = 'users/' . $_SESSION['uid'] . '/';
	$web_path = substr($_SERVER['PHP_SELF'], 0, strlen(strrchr($_SERVER['PHP_SELF'], '/')) * -1) . '/';
	$img_src = $web_path . $rel_user_path . 'images/';
	$full_web_path = 'http://' . $_SERVER['HTTP_HOST'] . $web_path;
	$full_img_src = $full_web_path . $rel_user_path . 'images/';
	// TODO: remove hard coding of template
	$template_img_src = $web_path . 'templates/cool/images/';
	$full_template_img_src = $full_web_path . 'templates/cool/images/';
	//echo "<p>Web path: {$web_path}</p>";
	//echo "<p>Img src: {$img_src}</p>";
	
	
	//$types = array('email', 'web', 'print', 'print bw');
	//$types = array('web', 'email');
	
	//echo "\n<br />POST:<br />";
	//print_r($_POST);
	//echo "\n<br />POST[newsletter]:<br />";
	//print_r($_POST['newsletter']);
	//echo "\n<br />POST[newsletter] noslash:<br />";
	//print_r(stripslashes($_POST['newsletter']));
	
	// collect the JSON containing all the data entered into the form
	$personal_info = json_decode(stripslashes($_POST['personal']), true);
	$newsletter_info = json_decode(stripslashes($_POST['newsletter']), true);
	
	//echo "\n<br />personal_info:<br />";
	//print_r($personal_info);
	//echo "\n<br />newsletter_info:<br />";
	//print_r($newsletter_info);
	
	// manipulate some of the data so it's easier to use later
	$num_pad = str_pad($newsletter_info['number'], 3, '0', STR_PAD_LEFT);
	$title_words = explode(' ', $newsletter_info['title']);
	$title_onset = array_shift($title_words);
	$title_coda = implode(' ', $title_words);
	$web_newsletter = $web_path . $rel_user_path . $newsletter_info['title'].'_'.$num_pad.'.html';
	
	// import the data from the template being used
	//include('templates/' . $newsletter_info['template'] . '/template.php');
	// TODO: remove hard coding of template
	include 'templates/cool/template.php';
	
	// do all this for each type of newsletter we are creating 
	foreach ($types as $type)
	{
		// fetch the file template
		$newsletter = $templateData[$type]['file'];
		
		// replace some of the placeholders with values the user has entered
		$newsletter = str_replace('<!--TITLE_ONSET-->', $title_onset, $newsletter);
		$newsletter = str_replace('<!--TITLE_CODA-->', $title_coda, $newsletter);
		//$newsletter = str_replace('<!--GREETING-->', $content->greeting, $newsletter);
		$newsletter = str_replace('<!--NUM-->', $newsletter_info['number'], $newsletter);
		$newsletter = str_replace('<!--0NUM-->', $num_pad, $newsletter);
		$newsletter = str_replace('<!--DATE-->', $newsletter_info['date'], $newsletter);
		$newsletter = str_replace('<!--WEB VERSION-->', $web_newsletter, $newsletter);
		if ($newsletter_info['subscribe']) {
			$newsletter = str_replace('<!--SUBSCRIBE-->', '<a href="' . $newsletter_info['subscribe'] . '">Click here to subscribe to the ' . $newsletter_info['title'] . ' newsletter.</a>', $newsletter);
		}
		if ($newsletter_info['unsubscribe']) {
			$newsletter = str_replace('<!--UNSUBSCRIBE-->', '<a href="' . $newsletter_info['unsubscribe'] . '">Click here to unsubscribe</a>', $newsletter);
		}
		if ($personal_info['website']) {
			$newsletter = str_replace('<!--PERSONAL WEBSITE-->', 'Visit <a href="' . $personal_info['website'] . '">our website</a> for more.', $newsletter);
		}
		if ($personal_info['websiteOrg'] && $personal_info['org']) {
			$newsletter = str_replace('<!--ORG WEBSITE-->', 'Visit <a href="' . $personal_info['websiteOrg'] . '">' . $personal_info['org'] . '</a> for more info.', $newsletter);
		}
		if ($personal_info['addressLine1']) {
			$newsletter = str_replace('<!--ADDRESS-->', "Write to us at {$personal_info['addressLine1']}, {$personal_info['addressLine2']}", $newsletter);
		}
		if ($personal_info['phone']) {
			$newsletter = str_replace('<!--PHONE-->', "Phone us {$personal_info['phone']}", $newsletter);
		}
		if ($personal_info['skype']) {
			$newsletter = str_replace('<!--SKYPE-->', "Skype us {$personal_info['skype']}", $newsletter);
		}
		
		// start building the content for the main articles
		$main_content = '';
		$main_first_article = True; // we don't want to insert the stuff that goes between articles before the first one
		
		// for every article we get from the entered data build it
		foreach ($newsletter_info['mainArticles'] as $article)
		{
		   if ($main_first_article) $main_first_article = False;
		   else {
		   	// if we're not on the first article add the stuff from the template that goes between articles
		      $main_content .= $templateData[$type]['betweenMainArticles'];
		   }
		   // get the frame of the article
		   $articleHTML = $templateData[$type]['mainArticle'];
		   // start building the content for the article
		   $articleContent = '';
		   $first_item = True;
		   // for every item in the article build it and add it to the article content
		   foreach ($article['article'] as $item) 
		   {
		   	if ($first_item) $first_item = False;
		   	else {
		   		// if we're not on the first item add the stuff from the template that goes between items
		   		$articleContent .= $templateData[$type]['mainItem']['betweenItems'];
		   	}
		   	// get the frame for the item
		   	$itemHTML = $templateData[$type]['mainItem'][$item['type']];
		   	// insert the content for the item and add it to the article content
		   	// for image file names we have to encode for URL
		   	if ($item['type'] == 'image') $item['value'] = rawurlencode($item['value']);
		   	$articleContent .= str_replace('<!--CONTENT-->', $item['value'], $itemHTML);
		   	// TODO: error handling for item names that don't fit the template array
		   }
		   // insert the content for the article into the article frame
		   //and add the whole thing to the content for the main articles
		   $main_content .= str_replace('<!--CONTENT-->', $articleContent, $articleHTML);
		}
		
		// now that we've built the main articles content add it to the newsletter
	   $newsletter = str_replace('<!--MAIN-->', $main_content, $newsletter);
	   
	   // now do all that again, but for the secondary articles
	   
		// start building the content for the secondary articles
		$secondary_content = '';
		$secondary_first_article = True; // we don't want to insert the stuff that goes between articles before the first one
		
		// for every article we get from the entered data, build it
		foreach ($newsletter_info['sideArticles'] as $article)
		{
		   if ($secondary_first_article) $secondary_first_article = False;
		   else {
		   	// if we're not on the first article add the stuff from the template that goes between articles
		      $secondary_content .= $templateData[$type]['betweenSecondaryArticles'];
		   }
		   // get the frame of the article
		   $articleHTML = $templateData[$type]['secondaryArticle'];
		   // start building the content for the article
		   $articleContent = '';
		   $first_item = True;
		   // for every item in the article build it and add it to the article content
		   foreach ($article['article'] as $item) 
		   {
		   	if ($first_item) $first_item = False;
		   	else {
		   		// if we're not on the first item add the stuff from the template that goes between items
		   		$articleContent .= $templateData[$type]['secondaryItem']['betweenItems'];
		   	}
		   	// get the frame for the item
		   	$itemHTML = $templateData[$type]['secondaryItem'][$item['type']];
		   	// insert the content for the item and add it to the article content
		   	$articleContent .= str_replace('<!--CONTENT-->', $item['value'], $itemHTML);
		   	// TODO: error handling for item names that don't fit the template array
		   }
		   // insert the content for the article into the article frame
		   //and add the whole thing to the content for the secondary articles
		   $secondary_content .= str_replace('<!--CONTENT-->', $articleContent, $articleHTML);
		}
		
		// now that we've built the secondary articles content add it to the newsletter
	   $newsletter = str_replace('<!--SECONDARY-->', $secondary_content, $newsletter);
	   //$newsletter = str_replace('<!--SECONDARY-->', 'SECONDARY CONTENT', $newsletter);
	   
	   // finally add the path to the images
	   $newsletter = str_replace('<!--IMAGE PATH-->', $img_src, $newsletter);
	   $newsletter = str_replace('<!--FULL IMAGE PATH-->', $full_img_src, $newsletter);
	   $newsletter = str_replace('<!--TEMPLATE IMAGE PATH-->', $template_img_src, $newsletter);
	   $newsletter = str_replace('<!--FULL TEMPLATE IMAGE PATH-->', $full_template_img_src, $newsletter);
	   // and style the links
	   $newsletter = str_replace('<a', "<a style=\"{$templateData[$type]['linkStyle']}\"", $newsletter);
	   
	   // create the filename
	   if ($type == 'web') {
		$filename = $newsletter_info['title'].'_'.$num_pad.'.html';
	    } else {
		$filename = $newsletter_info['title'].'_'.$num_pad.'_'.$type.'.html';
	    }
	    
	    // write the file
	    file_put_contents($rel_user_path . '/' . $filename, $newsletter);
	    
	    // output a link to the file which will go back into the user interface
	    echo "<a id=\"{$type}_file\" href=\"$rel_user_path$filename\">$type</a><br/>\n";
	}
} else {
	echo '<p>Newsletter not generated. Could not verify user. Login again.</p>';
}

?>
