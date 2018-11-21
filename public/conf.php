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
			'lib'=>'modules/Validate/ValidateManager.php'],
    'ds' => ['rq'=>'modules/DreamServersManager/index.php',//梦想服务模块
			'lib'=>'modules/DreamServersManager/DreamServersManager.php']#NEW_MODULES#
];

//错误配置
$fallbacks = [
	'1' => "已存在:#FALLTEXT#",
	'2' => "验证码错误",
    '3' => "未获取验证码",
    '4' => "还有#FALLTEXT#秒才能获取验证码",
    '5' => "不存在有效梦想池",
    '6' => "梦想池信息更新失败#FALLTEXT#",
    '7' => "梦想池购买结束",
    '8' => "身份校验错误",
    '9' => "用户注册失败",
    '10' => "用户信息更新失败",
    '11' => "未绑定手机",
    '12' => "未实名认证",
    '13' => "梦想提交失败",
    '14' => "梦想数量超过上限",
    '15' => "不存在用户",
	'16' => "验证码失效",
    '17' => "动作错误:#FALLTEXT#",
    '18' => "用户当日购买量超过上限",
    '19' => "购买信息创建失败",
    '20' => "购买信息更新错误",
	'37' => "已经实名认证",
	'38' => "实名认证审核中",
    '39' => "实名认证信息提交错误",
    '40' => "实名认证信息更新错误",
    '41' => "用户未通过实名认证",
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
        'command'=>"CREATE TABLE `#DBName#` ( `uid` TEXT NOT NULL COMMENT 'openid' , `nickname` TEXT NOT NULL , `headicon` TEXT NOT NULL , `tele` TEXT NOT NULL , `totalReward` INT NOT NULL COMMENT '总共奖励数量' , `totalJoin` INT NOT NULL COMMENT '总共参与数量' ,  `dayBuy` INT NOT NULL COMMENT '当日购买次数' , `identity` ENUM('USER','OWNER','ADMIN') NOT NULL , `ltime` INT NOT NULL COMMENT '最后购买时间' , PRIMARY KEY (`uid`(28))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT='用户管理';"
    ],
    'tPool'=>[
        'name'=>'dreampool',
        'command'=>"CREATE TABLE `#DBName#` ( `pid` TEXT NOT NULL COMMENT '池id' , `ptitle` TEXT NOT NULL COMMENT '池说明' , `uid` TEXT NOT NULL COMMENT '发布人id' , `state` ENUM('RUNNING','FINISHED') NOT NULL COMMENT '状态' , `tbill` INT NOT NULL COMMENT '目标金额' , `cbill` INT NOT NULL COMMENT '筹得金额' , `ubill` INT NOT NULL COMMENT '每份金额' , `duration` INT NOT NULL COMMENT '持续时间' , `ptime` INT NOT NULL COMMENT '发布时间' , `pcount` INT NOT NULL COMMENT '筹得份数' , PRIMARY KEY (`pid`(6))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT='梦想池';"
    ],
    'tOrder'=>[
        'name'=>'order',
        'command'=>"CREATE TABLE `#DBName#` ( `oid` TEXT NOT NULL , `uid` TEXT NOT NULL , `pid` TEXT NOT NULL , `bill` INT NOT NULL COMMENT '订单钱数' , `ctime` INT NOT NULL COMMENT '创建时间' , `ptime` INT NOT NULL COMMENT '支付时间' , `state` ENUM('SUBMIT','SUCCESS','FAILED','CANCEL') NOT NULL , `dcount` INT NOT NULL COMMENT '梦想份数' , `did` TEXT NOT NULL COMMENT '梦想id' , PRIMARY KEY (`oid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT='购买订单';"
    ],
    'tDream'=>[
        'name'=>'dream',
        'command'=>"CREATE TABLE `#DBName#` ( `did` TEXT NOT NULL COMMENT '梦想id' , `uid` TEXT NOT NULL COMMENT '梦想用户id' , `dtypeid` TEXT NOT NULL COMMENT '梦想类型id' , `dserverid` TEXT NOT NULL COMMENT '梦想规划服务id' , `title` TEXT NOT NULL COMMENT '梦想标题' , `content` TEXT NOT NULL COMMENT '梦想内容' , `videourl` TEXT NOT NULL COMMENT '梦想小视频地址' , `state` ENUM('SUBMIT','DOING','SUCCESS') NOT NULL , PRIMARY KEY (`did`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT='梦想';"
    ],
    'tDreamType'=>[
        'name'=>'dreamtype',
        'command'=>"CREATE TABLE `#DBName#` ( `dtid` TEXT NOT NULL COMMENT '梦想类型id' , `keyword` TEXT NOT NULL COMMENT '梦想简单分类' , `description` TEXT NOT NULL COMMENT '分类描述' , `online` BOOLEAN NOT NULL COMMENT '分类开启状态' , PRIMARY KEY (`dtid`(12), `keyword`(20))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT = '梦想类型';",
        'default'=>[
            '梦想类别:创业'=>[
                'dtid'=>'Enterprise',
                'keyword'=>'创业',
                'description'=>'有关于创业的梦想',
                'online'=>true
            ],
            '梦想类别:学习'=>[
                'dtid'=>'Learn',
                'keyword'=>'学习',
                'description'=>'有关于学习的梦想',
                'online'=>true
            ],
            '梦想类别:健身'=>[
                'dtid'=>'BodyBuild',
                'keyword'=>'健身',
                'description'=>'有关于健身的梦想',
                'online'=>true
            ]
        ]
    ],
    'tDreamServer'=>[
        'name'=>'dreamserver',
        'command'=>"CREATE TABLE `#DBName#` ( `dsid` TEXT NOT NULL COMMENT '梦想服务id' , `cost` INT NOT NULL COMMENT '花费（分）' , `dtid` TEXT NOT NULL COMMENT '梦想服务类别' , `title` TEXT NOT NULL COMMENT '服务标题' , `online` BOOLEAN NOT NULL COMMENT '上线状态' , PRIMARY KEY (`dsid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT = '梦想规划服务';",
        'default'=>[
            '默认创业规划服务'=>[
                'dsid'=>'ENTSERVER01',
                'cost'=>1000000,
                'dtid'=>'Enterprise',
                'title'=>'创业规划服务',
                'online'=>true
            ],
            '默认学习规划服务'=>[
                'dsid'=>'LERSERVER01',
                'cost'=>1000000,
                'dtid'=>'Learn',
                'title'=>'学习规划服务',
                'online'=>true
            ],
            '默认健身规划服务'=>[
                'dsid'=>'BDBSERVER01',
                'cost'=>1000000,
                'dtid'=>'BodyBuild',
                'title'=>'健身规划服务',
                'online'=>true
            ]
        ]
    ],
	'tValidate'=>[
		'name' => 'validate',
		'command'=> "CREATE TABLE `#DBName#` ( `tele` TEXT NOT NULL , `code` TEXT NOT NULL , `time` INT NOT NULL , PRIMARY KEY (`tele`(11))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT='验证码';"
	],
    'tLottery'=>[
        'name'=>'lottery',
        'command'=>"CREATE TABLE `#DBName#` ( `lid` TEXT NOT NULL COMMENT '排序号id' , `pid` TEXT NOT NULL COMMENT '梦想池id' , `uid` TEXT NOT NULL COMMENT '用户id' , `index` INT NOT NULL COMMENT '用户序号' , `oid` TEXT NOT NULL COMMENT '对应订单号' , `did` TEXT NOT NULL COMMENT '对应梦想号' , PRIMARY KEY (`lid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT='开奖号码';"
    ],
    'tAward'=>[
        'name'=>'award',
        'command'=>"CREATE TABLE `#DBName#` ( `pid` TEXT NOT NULL COMMENT '开奖梦想池id' , `uid` TEXT NOT NULL COMMENT '中奖用户id' , `lid` TEXT NOT NULL COMMENT '开奖编号' , `index` INT NOT NULL COMMENT '开奖排序号' , `atime` INT NOT NULL COMMENT '开奖时间' , `did` TEXT NOT NULL COMMENT '开奖梦想id' , `abill` INT NOT NULL COMMENT '开奖金额' , PRIMARY KEY (`lid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT='开奖结果';"
    ],
    'tId'=>[
        'name'=>'identity',
        'command'=>"CREATE TABLE `#DBName#` ( `uid` TEXT NOT NULL COMMENT '用户id' , `ccardfurl` TEXT NOT NULL COMMENT '银行卡正面图片地址' , `icardfurl` TEXT NOT NULL COMMENT '证件正面图片地址' , `icardburl` TEXT NOT NULL COMMENT '证件背面图片地址' , `ccardnum` TEXT NOT NULL COMMENT '银行卡号' , `icardnum` TEXT NOT NULL COMMENT '证件号' , `ftime` INT NOT NULL COMMENT '最后修改时间' , `state` ENUM('SUBMIT','SUCCESS','FAILED') NOT NULL COMMENT '审核状态' , PRIMARY KEY (`uid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8 COMMENT = '实名认证信息';"
    ]
];


ini_set('date.timezone','Asia/Shanghai');
?>