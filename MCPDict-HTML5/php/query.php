<?php
date_default_timezone_set("PRC");
require_once 'assemble.php';
require_once 'database.php';

$query_string = $_POST['string']; // string(x) : ""
$query_as = $_POST['mode']; // string(1) : 0~10
$query_flag = $_POST['flag']; // array(3) string(1)*3 : 0/1
$display_setting = $_POST['setting']; // array(5) mode*5


$code = new Code();
$query_item = $code->preprocess($query_string, $query_as);

$database = new Database($query_as, $query_flag);
$query_result = $database->mcpdict_query($query_item);

$tempa = new TemplatesAssemble($display_setting);
$HTML = $tempa->create_templates($query_result);

$result = ($HTML == "") ? 
		array('status' => "none") : 
		array('status' => "success", 'HTML' => $HTML) ;

echo json_encode($result);


