<?php 
$paid = [];
$paid['request'] = $_REQUEST;
if(isset($GLOBALS['HTTP_RAW_POST_DATA'])){
	$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
	$postStr = simplexml_load_string($postArr, 'SimpleXMLElement', LIBXML_NOCDATA);
	$paid['HTTP_RAW_POST_DATA'] = $postStr;
}
file_put_contents("paid.txt",json_encode($paid));
?>