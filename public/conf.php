<?php
//核心配置文件
define('KEY','konglf');
define('ADMIN','admin_init');
define('DEFDOWNLOAD','http://fitchain.pro');
//配置信息
$options = [
    'debug'  => false,//调试模式
    'token'  => 'konglf',
    'version'=> 'full',// 显示版本 可选值: concise/full
    'auth'=>false,//是否校验签名
    // 'aes_key' => null, // 可选
	'server' => '127.0.0.1',	//网站/数据库ip
    'test_server'=>'123.206.25.63',//测试数据库ip
    'admin'  => 'konglf2112',	//数据库用户名
    'password'  => '3226460036',	//数据库用户密码
	'database' => 'TinyDream', 	//数据库名
    'charset'=>'utf8mb4',
    'APP_ID' => 'wx1eed012d4550cf4c',//'wx8da643be043ceadc',//微信小程序AppID
    'APP_SECRET'=>'d64642153be2ddfbcb239e16b0dfb4ae',//微信小程序app secret
	'WEB_APP_ID'=>'wxc5216d15dd321ac5',//微信公众号app id  wxdca9f5edc034c1a0
	'WEB_APP_SECRET'=>'fe4b0a1b1a404ade990d6d0872547c4a',//微信公众号app secret e14f7b1ad2a43fb4977e5ce22456a0ec
	'combine_url'=>'tinydream.ivkcld.cn',
	'web_url'=>'http://tinydream.ivkcld.cn/TinydreamWeb',//微信公众号前端地址
	'web_url_redpack'=>'http://tinydream.ivkcld.cn/TinydreamWeb/html/RedEnvelope.html',//微信公众号前端地址
    'MCH_KEY'=>'xmxtinydream2018XMXTINYDREAM2018',//微信商户key
    'MCH_ID'=>'1520507531',//微信商户号
    'Notify_Url'=>'http://paysdk.weixin.qq.com/notify.php',//'http://www.antit.top:8001/fitback/index.php'
    'ManageIndex'=>'admin/index.html'
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
			'lib'=>'modules/DreamServersManager/DreamServersManager.php']
	,'ts' => ['rq'=>'modules/TestManager/index.php',//TestManager
			'lib'=>'modules/TestManager/TestManager.php']
	,'auth' => ['rq'=>'modules/AuthManager/index.php',//AuthManager
			'lib'=>'modules/AuthManager/AuthManager.php']
	,'admin' => ['rq'=>'modules/BackgroundController/index.php',//BackgroundController
			'lib'=>'modules/BackgroundController/BackgroundController.php']
	,'ub' => ['rq'=>'modules/UserBehaviourManager/index.php',//UserBehaviourManager
			'lib'=>'modules/UserBehaviourManager/UserBehaviourManager.php']
	,'no' => ['rq'=>'modules/NoticeManager/index.php',//NoticeManager
			'lib'=>'modules/NoticeManager/NoticeManager.php']
	,'cs' => ['rq'=>'modules/ConciseManager/index.php',//ConciseManager
			'lib'=>'modules/ConciseManager/ConciseManager.php']
	,'view' => ['rq'=>'modules/SnippetManager/index.php',//SnippetManager
			'lib'=>'modules/SnippetManager/SnippetManager.php']
	,'rp' => ['rq'=>'modules/RedPackManage/index.php',//RedPackManage
			'lib'=>'modules/RedPackManage/RedPackManage.php']
	,'paid' => ['rq'=>'modules/PaidManager/index.php',//PaidManager
			'lib'=>'modules/PaidManager/PaidManager.php']
	,'tr' => ['rq'=>'modules/TradeManager/index.php',//TradeManager
			'lib'=>'modules/TradeManager/TradeManager.php']
	,'co' => ['rq'=>'modules/ContractManager/index.php',//ContractManager
			'lib'=>'modules/ContractManager/ContractManager.php']
	,'op' => ['rq'=>'modules/OperationManager/index.php',//OperationManager
			'lib'=>'modules/OperationManager/OperationManager.php']#NEW_MODULES#
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
    '21' => "当前用户不存在这个梦想",
    '22' => "请求频率过高",
    '23' => "期号#FALLTEXT#已经开奖",
	'37' => "实名认证信息已经提交",
	'38' => "实名认证审核中",
    '39' => "实名认证信息提交错误",
    '40' => "实名认证信息更新错误",
    '41' => "用户未通过实名认证",
    '42' => "用户未提交实名认证信息",
    '43' => "实名认证审核参数不正确,应为SUCCESS或FAILED",
    '44' => "梦想更新失败",
    '45' => "该期梦想已失效",
    '46' => "梦想状态校验失败",
    '47' => "不存在梦想",
    '58' => "支付请求失败",
    '59' => "梦想池未开奖",
    '60' => "手机号不存在",
    '61' => "用户#FALLTEXT#无足够权限",
	'62' => "暂未开奖",
	'63' => "获取AccessToken失败",
    '64' => "密钥不存在",
    '65' => "密钥过期",
    '66' => "不存在该期开奖梦想互助",
    '67' => "超过红包最大个数",
    '68' => "支付请求失败：#FALLTEXT#",
    '69' => "梦想池剩余#FALLTEXT#个编号",
    '70' => "不存在该红包",
    '71' => "用户未提交梦想",
    '72' => "用户已领取该红包",
	'73' => "梦想互助未结束,红包均未失效问题",
    '74' => "用户已被领完",
    '75' => "订单已经支付成功",
    '76' => "不存在该需要退款的红包",
    '77' => "未找到数据文件:#FALLTEXT#",
    '78' => "未找到模板文件:#FALLTEXT#",
    '79' => "数据文件中未配置模板",
    '80' => "不存在有效开奖记录",
    '81' => "不存在合约:#FALLTEXT#",
    '82' => "用户已有正在进行的合约",
    '83' => "行动实例创建失败",
    '84' => "#FALLTEXT#已经打卡",
    '85' => "打卡数据错误",
    '86' => "未到打卡时间,打卡时间#FALLTEXT#",
    '87' => "不存在正在进行的行动或行动已结束",
    '88' => "补卡不在合理时间范围",
    '89' => "补卡次数不足",
    '90' => "当前日期#FALLTEXT#无需补卡",
    '91' => "补卡记录添加错误",
    '92' => "无#FALLTEXT#日打卡记录",
    '93' => "#FALLTEXT#日转发超时",
    '94' => "无该行动",
    '95' => "无该订单",
    '96' => "订单退款失败",
    '97' => "签名错误:#FALLTEXT#",
	'98' => "模块#FALLTEXT#不存在",
	'99' => "请求错误:#FALLTEXT#",
	'100' => "参数错误:#FALLTEXT#",
	'101' => "appid不匹配"
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
	'tAuth'=>[
		'name'=>'auth',
		'command'=>"CREATE TABLE `#DBName#` ( `uid` TEXT NOT NULL COMMENT '用户id' , `secret` INT NOT NULL COMMENT '密钥' , `time` INT NOT NULL COMMENT '时间戳' , PRIMARY KEY (`uid`(28))) ENGINE = InnoDB  DEFAULT CHARSET=UTF8MB4 COMMENT = '密钥管理';"
	],
	'tUser'=>[
	    'name'=>'user',
        'command'=>"CREATE TABLE `#DBName#` ( `uid` TEXT NOT NULL COMMENT 'openid' , `nickname` TEXT NOT NULL , `headicon` TEXT NOT NULL , `tele` TEXT NOT NULL , `totalReward` INT NOT NULL COMMENT '总共奖励数量' , `totalJoin` INT NOT NULL COMMENT '总共参与数量' ,  `dayBuy` INT NOT NULL COMMENT '当日购买次数' , `identity` ENUM('USER','OWNER','ADMIN') NOT NULL , `ltime` INT NOT NULL COMMENT '最后购买时间' , PRIMARY KEY (`uid`(28))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT='用户管理';"
    ],
    'tPool'=>[
        'name'=>'dreampool',
        'command'=>"CREATE TABLE `#DBName#` ( `pid` TEXT NOT NULL COMMENT '池id' , `ptitle` TEXT NOT NULL COMMENT '池说明' , `uid` TEXT NOT NULL COMMENT '发布人id' , `state` ENUM('RUNNING','FINISHED') NOT NULL COMMENT '状态' , `tbill` INT NOT NULL COMMENT '目标金额' , `cbill` INT NOT NULL COMMENT '筹得金额' , `ubill` INT NOT NULL COMMENT '每份金额' , `duration` INT NOT NULL COMMENT '持续时间' , `ptime` INT NOT NULL COMMENT '发布时间' , `pcount` INT NOT NULL COMMENT '筹得份数' ,`award` ENUM('NO','YES') NOT NULL DEFAULT 'NO' COMMENT '梦想池开奖状态' ,`ptype` ENUM('STANDARD','TRADE') NOT NULL DEFAULT 'STANDARD' COMMENT '梦想池类型' , PRIMARY KEY (`pid`(10))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT='梦想池';"
    ],
    'tOrder'=>[
        'name'=>'order',
        'command'=>"CREATE TABLE `#DBName#` ( `oid` TEXT NOT NULL , `uid` TEXT NOT NULL , `pid` TEXT NOT NULL , `bill` INT NOT NULL COMMENT '订单钱数' , `ctime` INT NOT NULL COMMENT '创建时间' , `ptime` INT NOT NULL COMMENT '支付时间' , `state` ENUM('SUBMIT','SUCCESS','FAILED','CANCEL') NOT NULL , `dcount` INT NOT NULL COMMENT '梦想份数' , `did` TEXT NOT NULL COMMENT '梦想id', `traid` TEXT NOT NULL COMMENT '微信商户id' , PRIMARY KEY (`oid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT='购买订单';"
    ],
    'tDream'=>[
        'name'=>'dream',
        'command'=>"CREATE TABLE `#DBName#` ( `did` TEXT NOT NULL COMMENT '梦想id' , `uid` TEXT NOT NULL COMMENT '梦想用户id' , `dtypeid` TEXT NOT NULL COMMENT '梦想类型id' , `dserverid` TEXT NOT NULL COMMENT '梦想规划服务id' , `title` TEXT NOT NULL COMMENT '梦想标题' , `content` TEXT NOT NULL COMMENT '梦想内容' , `videourl` TEXT NOT NULL COMMENT '梦想小视频地址' , `state` ENUM('SUBMIT','DOING','VERIFY','FAILED','SUCCESS') NOT NULL ,`payment` BOOLEAN NOT NULL COMMENT '付款标识' , PRIMARY KEY (`did`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT='梦想';"
    ],
    'tDreamType'=>[
        'name'=>'dreamtype',
        'command'=>"CREATE TABLE `#DBName#` ( `dtid` TEXT NOT NULL COMMENT '梦想类型id' , `keyword` TEXT NOT NULL COMMENT '梦想简单分类' , `description` TEXT NOT NULL COMMENT '分类描述' , `online` BOOLEAN NOT NULL COMMENT '分类开启状态' , PRIMARY KEY (`dtid`(12), `keyword`(20))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT = '梦想类型';",
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
        'command'=>"CREATE TABLE `#DBName#` ( `dsid` TEXT NOT NULL COMMENT '梦想服务id' , `cost` INT NOT NULL COMMENT '花费（分）' , `dtid` TEXT NOT NULL COMMENT '梦想服务类别' , `title` TEXT NOT NULL COMMENT '服务标题' , `online` BOOLEAN NOT NULL COMMENT '上线状态' , PRIMARY KEY (`dsid`(12))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT = '梦想规划服务';",
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
		'command'=> "CREATE TABLE `#DBName#` ( `tele` TEXT NOT NULL , `code` TEXT NOT NULL , `time` INT NOT NULL , PRIMARY KEY (`tele`(11))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT='验证码';"
	],
    'tLottery'=>[
        'name'=>'lottery',
        'command'=>"CREATE TABLE `#DBName#` ( `lid` TEXT NOT NULL COMMENT '排序号id' , `pid` TEXT NOT NULL COMMENT '梦想池id' , `uid` TEXT NOT NULL COMMENT '用户id' , `index` INT NOT NULL COMMENT '用户序号' , `oid` TEXT NOT NULL COMMENT '对应订单号' , `did` TEXT NOT NULL COMMENT '对应梦想号' , `state` ENUM('WAITTING','MISS','GET') NOT NULL COMMENT '梦想号状态'  , PRIMARY KEY (`lid`(40))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT='开奖号码';"
    ],
    'tAward'=>[
        'name'=>'award',
        'command'=>"CREATE TABLE `#DBName#` ( `pid` TEXT NOT NULL COMMENT '开奖梦想池id' , `uid` TEXT NOT NULL COMMENT '中奖用户id' ,`lid` TEXT NOT NULL COMMENT '开奖编号' ,  `expect` TEXT NOT NULL COMMENT '期号' , `code` TEXT NOT NULL COMMENT '开奖号码' , `index` INT NOT NULL COMMENT '开奖排序号' , `atime` INT NOT NULL COMMENT '开奖时间' , `did` TEXT NOT NULL COMMENT '开奖梦想id' , `abill` INT NOT NULL COMMENT '开奖金额', `imgurl` TEXT NOT NULL COMMENT '活动照片' , PRIMARY KEY (`pid`(10))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT='开奖结果';"
    ],
    'tId'=>[
        'name'=>'identity',
        'command'=>"CREATE TABLE `#DBName#` ( `uid` TEXT NOT NULL COMMENT '用户id' , `ccardfurl` TEXT NOT NULL COMMENT '银行卡正面图片地址' , `icardfurl` TEXT NOT NULL COMMENT '证件正面图片地址' , `icardburl` TEXT NOT NULL COMMENT '证件背面图片地址' , `ccardnum` TEXT NOT NULL COMMENT '银行卡号' , `icardnum` TEXT NOT NULL COMMENT '证件号' , `ftime` INT NOT NULL COMMENT '最后修改时间' , `state` ENUM('NONE','SUBMIT','SUCCESS','FAILED') NOT NULL COMMENT '审核状态' , PRIMARY KEY (`uid`(28))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT = '实名认证信息';"
    ],
    'tIdx'=>[
        'name'=>'identityx',
        'command'=>"CREATE TABLE `#DBName#` (`uid` TEXT NOT NULL COMMENT '用户id' ,`realname` TEXT NOT NULL COMMENT '真实姓名' , `icardnum` TEXT NOT NULL COMMENT '身份证号' , `ccardnum` TEXT NOT NULL COMMENT '银行卡号' , `bank` TEXT NOT NULL COMMENT '银行类别' , `openbank` TEXT NOT NULL COMMENT '开户行' , `icardfurl` TEXT NOT NULL COMMENT '手持证件正面照片地址' , `ftime` INT NOT NULL COMMENT '最后修改时间' , `state` ENUM('NONE','SUBMIT','SUCCESS','FAILED') NOT NULL COMMENT '审核状态' , PRIMARY KEY (`uid`(28))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT = '实名认证信息(新)';"
    ],
    'tBehave'=>[
        'name'=>'behaviour',
        'command'=>"CREATE TABLE `#DBName#` ( `ubid` TEXT NOT NULL COMMENT '每天统计id' , `date` TEXT NOT NULL COMMENT '日期' , `typeid` TEXT NOT NULL COMMENT '内容id' , `visit` INT NOT NULL COMMENT '访问数量' ,`join` INT NOT NULL COMMENT '参与数量' , `paid` INT NOT NULL COMMENT '支付数量' , PRIMARY KEY (`ubid`(40))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT = '行为统计';"
    ],
    'tNotice'=>[
        'name'=>'notice',
        'command'=>"CREATE TABLE `#DBName#` ( `nid` TEXT NOT NULL COMMENT '通知id' , `uid` TEXT NOT NULL COMMENT '用户id' , `content` TEXT NOT NULL COMMENT '消息内容' , `action` TEXT NOT NULL COMMENT '命令标识符' , `ptime` INT NOT NULL COMMENT '生成时间' , `state` ENUM('READ','UNREAD') NOT NULL COMMENT '阅读状态' , PRIMARY KEY (`nid`(20))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT = '通知表';"
    ],
    'tROrder'=>[
        'name'=>'redpackorder',
        'command'=>"CREATE TABLE `#DBName#`  (
          `rid` text NOT NULL COMMENT '红包id',
          `uid` text NULL COMMENT '用户openid',
          `pid` text NULL COMMENT '梦想互助id',
          `bill` int NULL COMMENT '价格',
          `rcount` int NULL COMMENT '数量',
          `gcount` int NULL COMMENT '获得的数量',
          `acount` int NULL COMMENT '人均份数',
          `content` text NULL COMMENT '祝福语',
          `rtype` enum('STANDARD','LUCKY') NULL DEFAULT 'STANDARD' COMMENT '红包类型',
          `ctime` int NULL COMMENT '创建时间',
          `ptime` int NULL COMMENT '支付时间',
          `state` enum('PAYMENT','RUNNING','FINISHED','REFUND') NULL COMMENT '订单状态',
          PRIMARY KEY (`rid`(12))
        ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '红包订单';"
    ],
    'tRReco'=>[
        'name'=>'redrecord',
        'command'=>"CREATE TABLE `#DBName#`  (
          `rpid` text NOT NULL COMMENT '领取序号',
          `uid` text NULL COMMENT '用户id',
          `rid` text NULL COMMENT '红包id',
          `gtime` int NULL COMMENT '领取时间',
          `pcount` int NULL COMMENT '份数',
          `oid` text NULL COMMENT '订单号',
          `pbill` int NULL COMMENT '金额',
          `index` int NULL COMMENT '序号',
          PRIMARY KEY (`rpid`(18))
        ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;"
    ],
    /***********小梦想互助简约版************/
    'dServer'=>[
        'name'=>'demo_server',
        'command'=>"CREATE TABLE `#DBName#` ( `hid` TEXT NOT NULL COMMENT '帮助id' , `uid` TEXT NOT NULL COMMENT '用户id', `title` TEXT NOT NULL COMMENT '梦想标题' , `content` TEXT NOT NULL COMMENT '梦想内容' , `server` TEXT NOT NULL COMMENT '服务类别', `bill` INT NOT NULL COMMENT '订单价格' ,`ctime` INT NOT NULL COMMENT '订单创建时间' , `ptime` INT NOT NULL COMMENT '订单支付时间' , `state` ENUM('SUBMIT','PAYMENT') NOT NULL COMMENT '订单状态' , PRIMARY KEY (`hid`(40) ,`uid`(28))) ENGINE = InnoDB DEFAULT CHARSET=UTF8MB4 COMMENT = '简约版服务列表'"
    ],
    'tTrade'=>[
        'name'=>'trade',
        'command'=>"CREATE TABLE `#DBName#` ( `tid` TEXT NOT NULL COMMENT '生意id' , `pid` TEXT NOT NULL COMMENT '梦想互助id' , `title` TEXT NOT NULL COMMENT '生意资料' , `url` TEXT NOT NULL COMMENT '生意资料页面' , `profit` INT NOT NULL COMMENT '利润' , PRIMARY KEY (`tid`(12))) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT = '生意信息';"
    ],
    'tContract'=>[
        'name'=>'contract',
        'command'=>"CREATE TABLE `#DBName#` ( `cid` TEXT NOT NULL COMMENT '合约id' , `title` TEXT NOT NULL COMMENT '标题' , `price` INT NOT NULL COMMENT '合约金额' , `durnation` INT NOT NULL COMMENT '时间周期' , `refund` INT NOT NULL COMMENT '退还金额' , `backrule` ENUM('EVERYDAY','END') NOT NULL COMMENT '退款规则（每日/结束时）', `attrule` ENUM('RELAY','NORMAL') NOT NULL COMMENT '打卡规则（转发后生效/打卡生效）' , `description` TEXT NOT NULL COMMENT '规则描述' , PRIMARY KEY (`cid`(12))) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT = '合约';",
        'default'=>[
            '21天合约'=>[
                "cid"=>"CO0000000001",
                "title"=>"21天行动合约",
                "price"=>9800,
                "durnation"=>21,
                "refund"=>9800,
                "backrule"=>"EVERYDAY",
				"attrule"=>"NORMAL",
                "description"=>"合约金98元，每天打卡，坚持21天，返还98元",
            ],
            '100天合约'=>[
                "cid"=>"CO0000000002",
                "title"=>"100天行动合约",
                "price"=>9900,
                "durnation"=>100,
                "refund"=>20000,
                "backrule"=>"END",
				"attrule"=>"RELAY",
                "description"=>"合约金99元，每天打卡并转发朋友圈，坚持100天，返还200元",
            ]
        ]
    ],
    'tOperation'=>[
        'name'=>'operation',
        'command'=>"CREATE TABLE `#DBName#` ( `opid` TEXT NOT NULL COMMENT '行动id' , `uid` TEXT NOT NULL COMMENT '用户id' , `cid` TEXT NOT NULL COMMENT '合约id' , `starttime` INT NOT NULL COMMENT '开始时间' , `lasttime` INT NOT NULL COMMENT '上次打卡时间' , `theme` TEXT NOT NULL COMMENT '主题字符串' , `alrday` INT NOT NULL COMMENT '已经打卡天数' , `conday` INT NOT NULL COMMENT '连续打卡天数' , `misday` INT NOT NULL COMMENT '漏卡天数' , `menday` INT NOT NULL COMMENT '补卡天数' , `menchance` INT NOT NULL COMMENT '补卡机会' , `invcount` INT NOT NULL COMMENT '邀请人数' , `state` ENUM('DOING','SUCCESS','FAILED') NOT NULL COMMENT '行动状态(进行，完成，失败)' , PRIMARY KEY (`opid`(12))) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT = '行动表';"
    ],
    'tAttend'=>[
        'name'=>'attendance',
        'command'=>"CREATE TABLE `#DBName#` ( `atid` TEXT NOT NULL COMMENT '打卡id' , `opid` TEXT NOT NULL COMMENT '行动id' , `uid` TEXT NOT NULL COMMENT '用户id' , `time` INT NOT NULL COMMENT '打卡时间' , `date` TEXT NOT NULL COMMENT '打卡日期' , `state` ENUM('RELAY','NOTRELAY','SUPPLY') NOT NULL COMMENT '打卡状态（转发,未转发,补卡）' , PRIMARY KEY (`atid`(15))) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = '打卡记录';"
    ],
    'tInvite'=>[
        'name'=>'invite',
        'command'=>"CREATE TABLE `#DBName#` ( `inid` TEXT NOT NULL COMMENT '邀请id' , `iuid` TEXT NOT NULL COMMENT '邀请者id' , `tuid` TEXT NOT NULL COMMENT '被邀请者id' , `opid` TEXT NOT NULL COMMENT '邀请者行动id' , `time` INT NOT NULL COMMENT '邀请时间' , `date` TEXT NOT NULL COMMENT '邀请日期' , PRIMARY KEY (`inid`(12))) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT = '邀请记录表';"
    ]
];


ini_set('date.timezone','Asia/Shanghai');
?>