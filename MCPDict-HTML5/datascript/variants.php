<?php
require_once '../php/code.php';

echo '<meta charset="UTF-8">';

$file_name = '../res/raw/orthography_hz_variants.txt';
$file = file($file_name);


$db = new SQLite3('../db/mcpdict.db', SQLITE3_OPEN_READWRITE);
if ($db) {
	$_string_ = "CREATE TABLE 'mcpdict_variants'(docid INTEGER PRIMARY KEY, 'unicode', 'json');";
	$db->exec($_string_);
	echo "open success";
} else {
	echo "failed";
}

$code = new Code();
foreach ($file as $line) {
	$line = trim($line);
	$line_arr = $code->escape($line);
	$key = $line_arr[0];
	$value = json_encode(array_slice($line_arr, 1));
	$_string_ = "INSERT INTO 'mcpdict_variants' VALUES (NULL, '".$key."', '".$value."');";
	$db->exec($_string_);
}

$db->close();
echo "<br>finished";
