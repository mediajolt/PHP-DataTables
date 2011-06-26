<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DataTables</title>
<link rel="stylesheet" href="datatables.css" type="text/css" media="screen" />
<script type="text/javascript" src="jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="datatables/media/js/jquery.dataTables.js"></script>
<style>
BODY {
	background-color: #f0f0f0;
	font-size: 100%;
	font-family: Arial, 'Helvetica Neue', Sans-Serif;
}

.th_white {
	color: #ffffff;
}

.th_black {
	color: #000000 !important;
}

.td_black {
	color: #000000 !important;
}

.tiny {
	font-size: 11px !important;
}

</style>
</head>
<body>

<div style="width: 900px;">

<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function print_array($array) {
	print '<pre>';
	print_r($array);
	print '</pre>';
}

require_once("datatables.php");

$table = new DataTable();
$table->id = "projects_table";
$table->user_id = "1";
$table->processor_path = "datatables_processor.php";
$table->data_source = "get_all_projects";
$table->default_count = 25;
$table->enable_excel = 1;
$table->enable_checkbox = 1;
$table->unique_id_field = "id";

$table->createAction(array(
	"label" => "Track Competitors in SERP Tracker",
	"js_function" => "alert('I am your action.');",
));

$table->createAction(array(
	"label" => "Stop Tracking in SERP Tracker",
	"js_function" => "alert('I am your action.');",
));

$table->createAction(array(
	"label" => "Remove Completely",
	"js_function" => "alert('I am your action.');",
));

$table->createHeaderDataCol(array(
	"id" => "keyword",
	"value" => "header keyword",
	"class" => "th_black tiny",
));

$table->createHeaderDataCol(array(
	"id" => "local_vol",
	"value" => "100",
	"class" => "th_black tiny",
));

$table->createHeaderDataCol(array(
	"id" => "global_vol",
	"value" => "200",
	"class" => "th_black tiny",
));

$table->createHeaderDataCol(array(
	"id" => "adv_comp",
	"value" => "0.2481",
	"class" => "th_black tiny",
));

$table->createHeaderDataCol(array(
	"id" => "req",
	"value" => "5",
	"class" => "th_black tiny",
));

$table->createHeaderDataCol(array(
	"id" => "act",
	"value" => "2",
	"class" => "th_black tiny",
));

$table->createHeaderDataCol(array(
	"id" => "ranking",
	"value" => "8",
	"class" => "th_black tiny",
));

$table->createHeaderDataCol(array(
	"id" => "options",
	"value" => "something",
	"class" => "th_black tiny",
));

$table->createHeader(array(
	"id" => "keyword",
	"label" => "Keyword",
	"field" => "keyword",
	"class" => "",
	"parent_id" => "",
	"default_sort" => "desc",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "adwords",
	"label" => "AdWords&trade; Statistics<br /><span class='tiny'>(Exact en-US)</span>",
	"field" => "",
	"class" => "th_black",
	"parent_id" => "",
	"default_sort" => "",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "local_vol",
	"label" => "Local Vol",
	"field" => "local_vol",
	"class" => "",
	"parent_id" => "adwords",
	"default_sort" => "",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "global_vol",
	"label" => "Global Vol",
	"field" => "global_vol",
	"class" => "",
	"parent_id" => "adwords",
	"default_sort" => "",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "adv_comp",
	"label" => "Adv Comp",
	"field" => "adv_comp",
	"class" => "",
	"parent_id" => "adwords",
	"default_sort" => "",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "links",
	"label" => "Links",
	"field" => "links",
	"class" => "",
	"parent_id" => "",
	"default_sort" => "",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "req",
	"label" => "Req",
	"field" => "req",
	"class" => "",
	"parent_id" => "links",
	"default_sort" => "",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "act",
	"label" => "Act",
	"field" => "act",
	"class" => "",
	"parent_id" => "links",
	"default_sort" => "",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "ranking",
	"label" => "Ranking",
	"field" => "ranking",
	"class" => "",
	"parent_id" => "",
	"default_sort" => "",
	"function" => "",
	"enable_excel" => "1",
));

$table->createHeader(array(
	"id" => "options",
	"label" => "Options",
	"field" => "options",
	"class" => "",
	"parent_id" => "",
	"default_sort" => "",
	"function" => "doSomething",
	"enable_excel" => "1",
));

$table_html = $table->init();

echo $table_html;

?>

</div>

</body>
</html>