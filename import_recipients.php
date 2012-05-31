<?php
require_once 'Excel/reader.php';
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
        // if full name is more than one word chop off last word
        $fullName = htmlspecialchars($data->sheets[0]['cells'][$row][2]);
        $space = strrpos($fullName, " ");
        if ($space === false) $name = $fullName;
        else $name = substr($fullName, 0, $space);
      }
   	echo "<tr class=\"recipient\"><td><input type=\"text\" class=\"name\" value=\"$name\" /></td><td><input type=\"text\" class=\"email\" value=\"$email\" /></td><td><button class=\"addGreeting\">Add personal greeting</button><input type=\"text\" class=\"greeting\" /></td></tr>";
    }
?>