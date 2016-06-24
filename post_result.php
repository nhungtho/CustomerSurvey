<?php
require_once("functions.php");
$post = $_GET["post"];
$start = array_search("_wp_http_referer",array_keys($_POST))+1;
$end = array_search("submit",array_keys($_POST))-1;
$newArr=array_slice($_POST, $start, $end);	
foreach($newArr as $k=>$v)
{
    // $k is the key name and $v is the value of that key
    echo $k."=".$v."<br />";  
    $val=mysql_real_escape_string($v);
    //$sql="INSERT INTO `SurveyResults` (`CustomerID`, `Question`, `Answer` ) VALUES (null, ".$k.", ".$val.")";
	$sql="INSERT INTO `SurveyResults` (`CustomerID`, `Question`, `Answer` ) VALUES ('abc', '$k', '$val')";
    mysql_query($sql);	
}

?>