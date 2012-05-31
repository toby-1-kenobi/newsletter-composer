<?php // db.php
 
$dbhost = 'localhost';
$dbuser = 'newslett_toby';
$dbpass = 'cursion10hb';
 
function dbConnect($dbname='newslett_composer') {
  global $dbhost, $dbuser, $dbpass;

  try {
    $dbh = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die("unable to connect to database");
  }
  return $dbh;
}
?>
