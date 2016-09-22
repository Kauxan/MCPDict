<?php
require_once '../php/code.php';

echo '<meta charset="UTF-8">';
$file_name_vn = '../res/raw/orthography_vn.tsv';

$code = new Code();

$file_vn = file($file_name_vn);
$i = 0;
$vn = array();
foreach ($file_vn as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	$vn[$sp1[0]] = $sp1[1].$sp1[2];
	$vn[$sp1[1].$sp1[2]] = $sp1[0];
}


$file_vn_json = '../json/vn.json';
file_put_contents($file_vn_json, json_encode($vn));
