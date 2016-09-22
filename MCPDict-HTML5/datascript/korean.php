<?php

$initials = array("g", "kk", "n", "d", "tt", "r", "m", "b", "pp", "s", "ss", "", "j", "jj", "ch", "k", "t", "p", "h");
$vowels = array("a", "ae", "ya", "yae", "eo", "e", "yeo", "ye", "o", "wa", "wae", "oe", "yo", "u", "wo", "we", "wi", "yu", "eu", "ui", "i");
$finals = array("", "k", "kk0", "ks0", "n", "nj0", "nh0", "d0", "l", "lg0", "lm0", "lb0", "ls0", "lt0", "lp0", "lh0", "m", "p", "bs0", "s0", "ss0", "ng", "j0", "ch0", "k0", "t0", "p0", "h0");

$kr_initials = array();
$kr_vowels = array();
$kr_finals = array();

$i = 0;
foreach ($initials as $init) {
	$kr_initials[$init] = $i++;
}

$i = 0;
foreach ($vowels as $vow) {
	$kr_vowels[$vow] = $i++;
}

$i = 0;
foreach ($finals as $fin) {
	$kr_finals[$fin] = $i++;
}

$file_kr_initials_json = '../json/kr_initials.json';
$file_kr_vowels_json = '../json/kr_vowels.json';
$file_kr_finals_json = '../json/kr_finals.json';

file_put_contents($file_kr_initials_json, json_encode($kr_initials));
file_put_contents($file_kr_vowels_json, json_encode($kr_vowels));
file_put_contents($file_kr_finals_json, json_encode($kr_finals));
