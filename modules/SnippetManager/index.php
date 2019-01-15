<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new SnippetManager()),
    [
        'inf'=>R('info'),//模块信息
		'build'=>R('BuildSnippet',['name','data'],PERMISSION_ALL),//模块信息
		'builds'=>R('BuildSnippets',['datas'],PERMISSION_ALL)
    ]);
?>