<?php
require_once '../php/code.php';

echo '<meta charset="UTF-8">';
$file_name_jp = '../res/raw/orthography_jp.tsv';

$code = new Code();

$file_jp = file($file_name_jp);
$i = 0;
$jp_hiragana = array();
$jp_katakana = array();
$jp_nippon = array();
$jp_hepburn = array();

foreach ($file_jp as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	for ($j = 0; $j < 4; $j++) { 
		$jp_hiragana[$sp1[$j]] = $sp1[0];
		$jp_katakana[$sp1[$j]] = $sp1[1];
		$jp_nippon[$sp1[$j]] = $sp1[2];
		$jp_hepburn[$sp1[$j]] = $sp1[3];
	}
}
$jp = array(
	'hiragana' => $jp_hiragana, 
	'katakana' => $jp_katakana, 
	'nippon' => $jp_nippon, 
	'hepburn' => $jp_hepburn
);


$file_jp_json = '../json/jp.json';
file_put_contents($file_jp_json, json_encode($jp));
