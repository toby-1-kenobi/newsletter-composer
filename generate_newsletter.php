<?php
require_once 'common.php';
function startsWith($haystack, $needle)
{
	return strpos($haystack, $needle, 0) === 0;
}

function splitText($text)
{
	$buffer = '';
	$output = array();
	foreach (explode('&#10;', $text) as $line)
	{
		if (startsWith($line, '- '))
		{
			if (strlen($buffer) > 0) {
				array_push($output, array("type" => 'para', "value" => $buffer));
				$buffer = '';
			}
			array_push($output, array("type" => 'list', "value" => $line));
		}
		else if (trim($line) === '')
		{
			if (strlen($buffer) > 0) {
				array_push($output, array("type" => 'para', "value" => $buffer));
				$buffer = '';
			}
		}
		else
		{
			if (strlen($buffer) > 0) {
				$buffer .= '<br />' . $line;
			} else {
				$buffer = $line;
			}
		}
	}
	if (strlen($buffer) > 0) {
		//$entry = {"type": 'para', "value": $buffer};
		array_push($output, array('type' => 'para', 'value' => $buffer));
	}
	return $output;
}

// build the html for an article from the content and the template.
// TODO: handle the case that there is no title.
function generateArticle($article, $template, $newsletterFormat, $section, $lastInserted)
{
	/*
	echo '<h3>generate article</h3>';
	echo '<strong>article</strong><br/>';
	print_r($article);
	echo "<br/><strong>newsletter Format</strong> $newsletterFormat";
	echo "<br/><strong>section</strong> $section";
	echo "<br/><strong>last inserted</strong> $lastInserted";
	* */
	$articleHTML = $template[$newsletterFormat]['between'][$section][$lastInserted.'-article'];
	$articleHTML .= $template[$newsletterFormat]['begin'][$section]['article'];
	foreach ($article['article'] as $item)
	{
		if ($item['type'] === 'image')
		{
			$articleHTML .= generateArticleItem($item, $template, $newsletterFormat, $section, $lastInserted);
			$lastInserted = 'image';
		}
		else
		{
			foreach (splitText($item['value']) as $newItem)
			{
				$articleHTML .= generateArticleItem($newItem, $template, $newsletterFormat, $section, $lastInserted);
				$lastInserted = $newItem['type'];
			}
		}
	}
	$articleHTML .= $template[$newsletterFormat]['end'][$section]['article'];
	$articleHTML = str_replace('<!--ARTICLE TITLE-->', $article['title'], $articleHTML);
	return $articleHTML;
}

// build the HTML for one part of an article
function generateArticleItem($item, $template, $newsletterFormat, $section, $lastInserted)
{
	/*
	echo '<h4>generate item</h4>';
	echo '<strong>item</strong><br/>';
	print_r($item);
	echo "<br/><strong>newsletter Format</strong> $newsletterFormat";
	echo "<br/><strong>section</strong> $section";
	echo "<br/><strong>last inserted</strong> $lastInserted";
	* */
	$itemHTML = '';
	if ($template[$newsletterFormat]['between'][$section][$lastInserted.'-'.$item['type']])
	{
		$itemHTML .= $template[$newsletterFormat]['between'][$section][$lastInserted.'-'.$item['type']];
	} else {
		if ($last_inserted == 'para' or $last_inserted == 'list' or $last_inserted == 'image')
		{
			$itemHTML .= $template[$newsletterFormat]['between'][$section]['items'];
		}
	}
	$itemHTML .= str_replace('<!--CONTENT-->', $item['value'], $template[$newsletterFormat]['whole'][$section][$item['type']]);
	return $itemHTML;
}

// check user is logged in
if (login_ok() == 1) {

	//echo '<p>login ok</p>';
	
	// get the paths to the images
	$rel_user_path = 'users/' . $_SESSION['uid'] . '/';
	$web_path = substr($_SERVER['PHP_SELF'], 0, strlen(strrchr($_SERVER['PHP_SELF'], '/')) * -1) . '/';
	$img_src = $web_path . $rel_user_path . 'images/';
	$full_web_path = 'http://' . $_SERVER['HTTP_HOST'] . $web_path;
	$full_img_src = $full_web_path . $rel_user_path . 'images/';
	// TODO: remove hard coding of template
	$template_img_src = $web_path . 'templates/cool/images/';
	$full_template_img_src = $full_web_path . 'templates/cool/images/';
	//echo "<p>Full Web path: {$full_web_path}</p>";
	//echo "<p>Full Img src: {$full_img_src}</p>";
	
	
	//$types = array('email', 'web', 'print', 'print bw');
	//$types = array('web', 'email');
	
	//echo "\n<br />POST:<br />";
	//print_r($_POST);
	//echo "\n<br />POST[newsletter]:<br />";
	//print_r($_POST['newsletter']);
	//echo "\n<br />POST[newsletter] noslash:<br />";
	//print_r(stripslashes($_POST['newsletter']));
	
	// collect the JSON containing all the data entered into the form
	//$personal_info = json_decode(stripslashes($_POST['personal']), true);
	$newsletter_info = json_decode(stripslashes($_POST['newsletter']), true);

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
	// TODO: remove hard coding of template file to import
	include 'templates/cool/template.php';

	// do all this for each type of newsletter we are creating (the $types array comes from the template file)
	foreach ($types as $type)
	{
		// keep track of the last element iserted into our newsletter so we know what in between bits to put in before the next.
		$last_inserted = '';
		
		// build the newsletter
		$newsletter = $template[$type]['begin']['newsletter']['container'];
		$newsletter .= $template[$type]['begin']['main']['container'];
		// for every article we get from the entered data build it
		foreach ($newsletter_info['mainArticles'] as $article)
		{
			$newsletter .= generateArticle($article, $template, $type, 'main', $last_inserted);
			$last_inserted = 'article';
		}
		$newsletter .= $template[$type]['end']['main']['container'];
		$last_inserted = 'main';
		$newsletter .= $template[$type]['begin']['secondary']['container'];		
		// for every article we get from the entered data build it
		foreach ($newsletter_info['sideArticles'] as $article)
		{
			$newsletter .= generateArticle($article, $template, $type, 'secondary', $last_inserted);
			$last_inserted = 'article';
		}
		$newsletter .= $template[$type]['end']['secondary']['container'];
		$last_inserted = 'secondary';
		$newsletter .= $template[$type]['end']['newsletter']['container'];
		
		// build the header
		$header = $template[$type]['begin']['header']['container'];
		foreach ( explode('&#10;', $newsletter_info['header'][$type]) as $header_line)
		{
			if ($header_line == '') $header_line = '&nbsp;';
			$header .= str_replace('<!--CONTENT-->', $header_line, $template[$type]['headerText']);
		}
		$header .= $template[$type]['end']['header']['container'];
		
		// insert the header
		$newsletter = str_replace('<!--HEADER-->', $header, $newsletter);
		
		// build the footer
		$footer = $template[$type]['begin']['footer']['container'];
		foreach ( explode('&#10;', $newsletter_info['footer'][$type]) as $footer_line)
		{
			if ($footer_line == '') $footer_line = '&nbsp;';
			$footer .= str_replace('<!--CONTENT-->', $footer_line, $template[$type]['footerText']);
		}
		$footer .= $template[$type]['end']['footer']['container'];
		
		// insert the footer
		$newsletter = str_replace('<!--FOOTER-->', $footer, $newsletter);
		
		
		// replace some of the placeholders with values the user has entered
		$newsletter = str_replace('<!--TITLE_ONSET-->', $title_onset, $newsletter);
		$newsletter = str_replace('<!--TITLE_CODA-->', $title_coda, $newsletter);
		//$newsletter = str_replace('<!--GREETING-->', $content->greeting, $newsletter);
		$newsletter = str_replace('<!--NUM-->', $newsletter_info['number'], $newsletter);
		$newsletter = str_replace('<!--0NUM-->', $num_pad, $newsletter);
		$newsletter = str_replace('<!--DATE-->', $newsletter_info['date'], $newsletter);
		
		/*
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
	   */
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
