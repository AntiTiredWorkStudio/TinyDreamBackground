<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new AuthManager()),
    [
        'inf'=>R('info',null,PERMISSION_AUTH_FREE),//模块信息
        'au'=>R('CheckAuthToken',['secret','timeStamp','openid'],PERMISSION_AUTH_FREE),//校验验证信息
    ]);
?>