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
    'aw' => ['rq'=>'modules/Award/index.php',//奖励模块
        'lib'=>'modules/Award/AwardManager.php'],
    'dr' => ['rq'=>'modules/Dream/index.php',//梦想模块
        'lib'=>'modules/Dream/DreamManager.php'],
    'dp' => ['rq'=>'modules/DreamPool/index.php',//梦想池模块
        'lib'=>'modules/DreamPool/DreamPoolManager.php'],
    'us' => ['rq'=>'modules/User/index.php',//用户模块
        'lib'=>'modules/User/UserManager.php'],
	'va' => ['rq'=>'modules/Validate/index.php',//验证码模块
			'lib'=>'modules/Validate/ValidateManager.php']#NEW_MODULES#
];

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
	'99' => "请求错误:#FALLTEXT#",
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
	/*'tUser' => [
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
	],*/
	'tUser'=>[
	    'name'=>'user',
        'command'=>"CREATE TABLE `#DBName#` ( `uid` TEXT NOT NULL COMMENT 'openid' , `nickname` TEXT NOT NULL , `headicon` TEXT NOT NULL , `tele` TEXT NOT NULL , `totalReward` INT NOT NULL COMMENT '总共购买数量' , `dayBuy` INT NOT NULL COMMENT '当日购买次数' , `identity` ENUM('user','owner','admin') NOT NULL , `ltime` INT NOT NULL COMMENT '最后购买时间' , PRIMARY KEY (`uid`(28))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;"
    ],
    'tPool'=>[
        'name'=>'dreampool',
        'command'=>"CREATE TABLE `#DBName#` ( `pid` TEXT NOT NULL COMMENT '池id' , `ptitle` TEXT NOT NULL COMMENT '池说明' , `uid` TEXT NOT NULL COMMENT '发布人id' , `state` ENUM('RUNNING','FINISHED') NOT NULL COMMENT '状态' , `tbill` INT NOT NULL COMMENT '目标金额' , `cbill` INT NOT NULL COMMENT '筹得金额' , `ubill` INT NOT NULL COMMENT '每份金额' , `duration` INT NOT NULL COMMENT '持续时间' , `ptime` INT NOT NULL COMMENT '发布时间' , `pcount` INT NOT NULL COMMENT '筹得份数' , PRIMARY KEY (`pid`(6))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;"
    ],
    'tOrder'=>[
        'name'=>'order',
        'command'=>"CREATE TABLE `#DBName#` ( `oid` TEXT NOT NULL , `uid` TEXT NOT NULL , `pid` TEXT NOT NULL , `bill` INT NOT NULL COMMENT '订单钱数' , `ctime` INT NOT NULL COMMENT '创建时间' , `ptime` INT NOT NULL COMMENT '支付时间' , `state` ENUM('SUBMIT','SUCCESS','FAILED','CANCEL') NOT NULL , `dcount` INT NOT NULL COMMENT '梦想份数' , `did` TEXT NOT NULL COMMENT '梦想id' , PRIMARY KEY (`oid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;"
    ],
    'tDream'=>[
        'name'=>'dream',
        'command'=>"CREATE TABLE `#DBName#` ( `did` TEXT NOT NULL COMMENT '梦想id' , `uid` INT NOT NULL COMMENT '梦想用户id' , `title` INT NOT NULL COMMENT '梦想标题' , `content` INT NOT NULL COMMENT '梦想内容' , `videourl` INT NOT NULL COMMENT '梦想小视频地址' , `state` ENUM('SUBMIT','DOING','SUCCESS') NOT NULL , PRIMARY KEY (`did`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;"
    ],
	'tValidate'=>[
		'name' => 'validate',
		'command'=> "CREATE TABLE `#DBName#` ( `tele` TEXT NOT NULL , `code` TEXT NOT NULL , `time` INT NOT NULL , PRIMARY KEY (`tele`(11))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;"
	],
    'tLottery'=>[
        'name'=>'lottery',
        'command'=>"CREATE TABLE `#DBName#` ( `lid` TEXT NOT NULL COMMENT '排序号id' , `pid` TEXT NOT NULL COMMENT '梦想池id' , `uid` TEXT NOT NULL COMMENT '用户id' , `index` INT NOT NULL COMMENT '用户序号' , `oid` TEXT NOT NULL COMMENT '对应订单号' , `did` TEXT NOT NULL COMMENT '对应梦想号' , PRIMARY KEY (`lid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;"
    ],
    'tAward'=>[
        'name'=>'award',
        'command'=>"CREATE TABLE `#DBName#` ( `pid` TEXT NOT NULL COMMENT '开奖梦想池id' , `uid` TEXT NOT NULL COMMENT '中奖用户id' , `lid` TEXT NOT NULL COMMENT '开奖编号' , `index` INT NOT NULL COMMENT '开奖排序号' , `atime` INT NOT NULL COMMENT '开奖时间' , `did` TEXT NOT NULL COMMENT '开奖梦想id' , `abill` INT NOT NULL COMMENT '开奖金额' , PRIMARY KEY (`pid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8;"
    ]
];


ini_set('date.timezone','Asia/Shanghai');
?>