<?php
//核心配置文件
define('KEY','konglf');
define('ADMIN','admin_init');
define('DEFDOWNLOAD','http://fitchain.pro');
//配置信息
$options = [
    'debug'  => true,//调试模式
    'token'  => 'konglf',
    // 'aes_key' => null, // 可选
	'server' => '127.0.0.1',	//网站/数据库ip
    'admin'  => 'konglf2112',	//数据库用户名
    'password'  => '3226460036',	//数据库用户密码
	'database' => 'TinyDream', 	//数据库名
    'APP_ID' => 'wx8da643be043ceadc',//微信AppID
    'MCH_KEY'=>'fitminer12345678FITMINER12345678',//微信商户key
    'MCH_ID'=>'1514357161',//微信商户号
    'Notify_Url'=>'http://paysdk.weixin.qq.com/notify.php'//'http://www.antit.top:8001/fitback/index.php'
];

//模块配置
$modules = [
	'db' => ['rq'=>'modules/DataBase/index.php',//数据库模块
			'lib'=>'modules/DataBase/DBManager.php'],
	'va' => ['rq'=>'modules/Validate/index.php',//验证码模块
			'lib'=>'modules/Validate/ValidateManager.php']
];

//矿机订单
/*$orderprefix=[
	'mini' => 1,//迷你矿机
	'mid' => 2,//人气矿机
	'max' => 3,//精品矿机
	'ultra' => 4 //超级矿机
];*/

//错误配置
$fallbacks = [
	'1' => "已存在:#FALLTEXT#",
	'2' => "验证码错误",
	'3' => "未获取验证码",
	'4' => "还有#FALLTEXT#秒才能获取验证码",
	'5' => "密匙过期,需重新进行验证",
	'6' => "不存在密钥,需进行验证",
	'10' => "密钥错误,访问失败",
	'11' => "注册失败",
	'12' => "已经存在该用户,不能注册",
	'13' => "不存在该用户,不能登录",
	'14' => "订单创建错误",
	'15' => "错误的矿机类型",
	'16' => "验证码失效",
	'17' => "订单更新失败",
	'18' => "矿机建立失败",
	'19' => "无更新参数",
	'20' => "矿机信息更新失败",
	'21' => "矿机信息删除失败",
	'22' => "找不到矿机信息",
	'23' => "矿机信息已经存在",
	'24' => "矿机信息获取失败",
	'25' => "矿机价格信息校验失败,订单失效",
	'26' => "矿机订单已失效",
	'27' => "该类矿机已售罄",
	'28' => "矿机订单已超过支付时间",
	'29' => "没有购买矿机",
	'30' => "添加矿机失败",
	'31' => "查找矿机失败",
	'32' => "矿机还未生效",
	'33' => "矿机购买信息校验错误",
	'34' => "矿机信息更新失败",
	'35' => "没有生效的矿机",
	'36' => "挖矿信息更新失败",
	'37' => "已经实名认证",
	'38' => "实名认证审核中",
    '39' => "实名认证信息提交错误",
    '40' => "实名认证信息更新错误",
    '41' => "用户未通过实名认证",
    '42' => "提现申请提交失败",
    '43' => "提现请求获取失败",
    '44' => "不存在该用户",
    '45' => "不存权限#FALLTEXT#",
    '46' => "权限不匹配",
    '47' => "授权过时",
    '48' => "权限查询错误",
    '49' => "用户权限不足",
    '50' => "权限更新失败",
    '51' => "实名认证信息获取失败",
    '52' => "提现请求更新失败",
	'53' => "该类型矿机已经被购买",
    '54' => "订单获取失败",
    '55' => "正在处理的提现次数超过上限",
    '56' => "账户余额不足",
    '57' => "提现请求已处理完成",
    '58' => "支付请求失败",
    '59' => "缺少参数,设置无效",
    '60' => "更新失败",
    '61' => "插入失败",
    '62' => "未找到请求版本",
    '63' => "应用版本过低,需要更新",
	'98' => "模块不存在",
	'99' => "请求错误",
	'100' => "参数错误:#FALLTEXT#"
];

//权限级别定义
$permissions = [
    'admin'=>10,//超级管理员
    'manage'=>5,//管理员
    'user'=>1//用户
];

//数据库配置
$tables = [
	'tUser' => [
		'name'=>'user',
		'command'=> "CREATE TABLE `#DBName#` ( `tele` TEXT NOT NULL , `openid` TEXT NOT NULL , `time` INT NOT NULL , `uuid` TEXT NOT NULL , `state` TEXT NOT NULL , PRIMARY KEY (`tele`(11))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;",
        'default'=>[
            'admin'=>[
                'tele'=>'10000000000',
                'openid'=>'',
                'time'=>time(),
                'uuid'=>'-1',
                'state'=> ADMIN
            ]
        ]
	],
	'tValidate'=>[
		'name' => 'validate',
		'command'=> "CREATE TABLE `#DBName#` ( `tele` TEXT NOT NULL , `code` TEXT NOT NULL , `time` INT NOT NULL , PRIMARY KEY (`tele`(11))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;"
	]
];


ini_set('date.timezone','Asia/Shanghai');
?>