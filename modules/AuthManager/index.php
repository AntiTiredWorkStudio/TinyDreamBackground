<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new AuthManager()),
    [
        'inf'=>R('info',null,PERMISSION_AUTH_FREE)//模块信息
    ]);
?>