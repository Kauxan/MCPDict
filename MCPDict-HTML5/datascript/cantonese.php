<?php
require_once '../php/code.php';

echo '<meta charset="UTF-8">';
$file_name_ct_initials = '../res/raw/orthography_ct_initials.tsv';
$file_name_ct_finals = '../res/raw/orthography_ct_finals.tsv';

$code = new Code();

// initials
$file_ct_initials = file($file_name_ct_initials);
$i = 0;
$init_J2C = array();
$init_J2Y = array();
$init_J2L = array();
$init_C2J = array();
$init_Y2J = array();
$init_L2J = array();
foreach ($file_ct_initials as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	$sp2 = array_slice($sp1, 0, 4);
	$init_J2C[$sp2[0]] = $sp2[1];
	$init_J2Y[$sp2[0]] = $sp2[2];
	$init_J2L[$sp2[0]] = $sp2[3];
	$init_C2J[$sp2[1]] = $sp2[0];
	$init_Y2J[$sp2[2]] = $sp2[0];
	$init_L2J[$sp2[3]] = $sp2[0];
}
$ct_initials = array(
	'J2C' => $init_J2C, 
	'J2Y' => $init_J2Y, 
	'J2L' => $init_J2L, 
	'C2J' => $init_C2J, 
	'Y2J' => $init_Y2J, 
	'L2J' => $init_L2J
);

// finals
$file_ct_finals = file($file_name_ct_finals);
$i = 0;
$fin_J2C = array();
$fin_J2Y = array();
$fin_J2L = array();
$fin_C2J = array();
$fin_Y2J = array();
$fin_L2J = array();
foreach ($file_ct_finals as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	$sp2 = array_slice($sp1, 0, 4);
	$fin_J2C[$sp2[0]] = $sp2[1];
	$fin_J2Y[$sp2[0]] = $sp2[2];
	$fin_J2L[$sp2[0]] = $sp2[3];
	$fin_C2J[$sp2[1]] = $sp2[0];
	$fin_Y2J[$sp2[2]] = $sp2[0];
	$fin_L2J[$sp2[3]] = $sp2[0];
}
$ct_finals = array(
	'J2C' => $fin_J2C, 
	'J2Y' => $fin_J2Y, 
	'J2L' => $fin_J2L, 
	'C2J' => $fin_C2J, 
	'Y2J' => $fin_Y2J, 
	'L2J' => $fin_L2J
);


$file_ct_initials_json = '../json/ct_initials.json';
$file_ct_finals_json = '../json/ct_finals.json';

file_put_contents($file_ct_initials_json, json_encode($ct_initials));
file_put_contents($file_ct_finals_json, json_encode($ct_finals));
