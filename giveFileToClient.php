<?php
header("Content-type: {$_POST['type']}");
header("Content-Disposition: attachment; filename=\"{$_POST['filename']}\"");
echo $_POST['content'];
?>