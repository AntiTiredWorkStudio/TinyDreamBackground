<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new SnippetManager()),
    [
        'inf'=>R('info'),//模块信息
		'build'=>R('BuildSnippet',['name','data','#url'],PERMISSION_ALL),//模块信息
		'builds'=>R('BuildSnippets',['datas','#url'],PERMISSION_ALL),
        'build_dt'=>R('BuildTemplate',['turl','#root'],PERMISSION_ALL),//数据&模板构建页面
        'tlist'=>R('TemplateList'),//获取模板列表
		'build_json'=>R('BuildJson',['turl','#root','#datas'],PERMISSION_ALL),//创建json
		'upload_img'=>R('BuildUploadImgList',['imglist'],PERMISSION_ALL),//配置文件表
        'filelist'=>R('UploadFileInfo',['#seek','#count'],PERMISSION_ALL),
		'sql'=>R('RunSql',['name'],PERMISSION_ALL),//执行sql文件
    ]);
?>