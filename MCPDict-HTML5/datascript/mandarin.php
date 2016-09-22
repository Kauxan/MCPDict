<?php
require_once '../php/code.php';

echo '<meta charset="UTF-8">';
$file_name_pu_bpmf = '../res/raw/orthography_pu_bopomofo.tsv';
$file_name_pu_pinyin = '../res/raw/orthography_pu_pinyin.tsv';

$code = new Code();

// pinyin
$file_pu_pinyin = file($file_name_pu_pinyin);
$i = 0;
$pu_pinyin = array();
foreach ($file_pu_pinyin as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	$sp2 = array_slice($sp1, 0, 3);
	$pu_pinyin[$sp2[0]] = $sp2[1].$sp2[2];
	$pu_pinyin[$sp2[1].$sp2[2]] = $sp2[0];
}

// Bopomofo
$file_pu_bpmf = file($file_name_pu_bpmf);
$i = 0;
$pu_FromBopomofoTone = array();
$pu_ToBopomofoTone = array();
$pu_FromBopomofoPartial = array();
$pu_ToBopomofoPartial = array();
$pu_FromBopomofoWhole = array();
$pu_ToBopomofoWhole = array();
foreach ($file_pu_bpmf as $line) {
	if (!($i++)) continue;
	$sp1 = $code->remove_white($line);
	if (in_array($sp1[1], array('2', '3', '4', '_'))) {
		$pu_FromBopomofoTone[$sp1[0]] = $sp1[1];
		$pu_ToBopomofoTone[$sp1[1]] = $sp1[0];
	} else {
		$pu_FromBopomofoPartial[$sp1[0]] = $sp1[1];
		$pu_ToBopomofoPartial[$sp1[1]] = $sp1[0];
		if (count($sp1) > 2) {
			$pu_FromBopomofoWhole[$sp1[0]] = $sp1[2];
			$pu_ToBopomofoWhole[$sp1[2]] = $sp1[0];
		}
	}
}
$pu_bpmf = array(
	'from_Tone' => $pu_FromBopomofoTone, 
	'to_Tone' => $pu_ToBopomofoTone, 
	'from_Partial' => $pu_FromBopomofoPartial, 
	'to_Partial' => $pu_ToBopomofoPartial, 
	'from_Whole' => $pu_FromBopomofoWhole, 
	'to_Whole' => $pu_ToBopomofoWhole
);


$file_pu_pinyin_json = '../json/pu_pinyin.json';
$file_pu_bpmf_json = '../json/pu_bpmf.json';

file_put_contents($file_pu_pinyin_json, json_encode($pu_pinyin));
file_put_contents($file_pu_bpmf_json, json_encode($pu_bpmf));
