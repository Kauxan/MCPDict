<?php
require_once '../php/code.php';

echo '<meta charset="UTF-8">';
$file_name_mc_initials = '../res/raw/orthography_mc_initials.tsv';
$file_name_mc_finals = '../res/raw/orthography_mc_finals.tsv';
$file_name_mc_bieng_sjyix = '../res/raw/orthography_mc_bieng_sjyix.tsv';

$code = new Code();

// 声母
$file_mc_initials = file($file_name_mc_initials);
$i = 0;
$mc_initials = array();
foreach ($file_mc_initials as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	$sp2 = array_slice($sp1, 0, 2);
	if ($sp2[0] == "_") $sp2[0] = "";
	$mc_initials[$sp2[0]] = $sp2[1]; // 拼音 => 声母
}

// 韻母
$file_mc_finals = file($file_name_mc_finals);
$i = 0;
$mc_finals = array();
foreach ($file_mc_finals as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	$sp2 = array_slice($sp1, 0, 5);
	$mc_finals[$sp2[0]] = array_slice($sp2, 1); // 拼音 => 攝 等 呼 韻母
}

// 平水韻
$file_mc_bieng_sjyix = file($file_name_mc_bieng_sjyix);
$i = 0;
$mc_bieng_sjyix = array();
foreach ($file_mc_bieng_sjyix as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	$sp2 = array_slice($sp1, 0, 2);
	$string = $sp2[1];
	for ($x = 0; $x < mb_strlen($string, 'UTF-8'); $x++) { 
		$str = mb_substr($string, $x, 1, 'UTF-8');
		$mc_bieng_sjyix[$str] = $sp2[0]; // 廣韻韻目 => 平水韻韻目
	}
}


$file_mc_initials_json = '../json/mc_initials.json';
$file_mc_finals_json = '../json/mc_finals.json';
$file_mc_bieng_sjyix_json = '../json/mc_bieng_sjyix.json';

file_put_contents($file_mc_initials_json, json_encode($mc_initials));
file_put_contents($file_mc_finals_json, json_encode($mc_finals));
file_put_contents($file_mc_bieng_sjyix_json, json_encode($mc_bieng_sjyix));
