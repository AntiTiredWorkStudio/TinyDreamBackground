<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-12
 * Time: 下午 11:12
 */

define('WECHAT_ACCESSTOKEN_COMMAND','accesstoken');
define('WECHAT_COMBINE_COMMAND','signature');
define('WECHAT_GETUSERINFO_COMMAND','code');
define('WECHAT_WEB_COMMAND','webchatweb');
define('WECHAT_MENU_COMMAND','wechatmenu');
define('WECHAT_IMAGE_COMMAND','wechatimage');
define('WECHAT_CUSTOMER_COMMAND','wechatcustomer');
define('WECHAT_TEXT_COMMAND','wechattext');



function CombineWechatServer(){
    //{"signature":"083289d5f4f57622dec53bbffeeb84492a7125cc","echostr":"3749999956182686711","timestamp":"1547194837","nonce":"1203941899"}
    $signature = $_REQUEST['signature'];
    $echostr = $_REQUEST['echostr'];
    $timestamp = $_REQUEST['timestamp'];
    $nonce = $_REQUEST['nonce'];
    $token = 'konglf01';
    $tmpArr = array($token,$timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);
    file_put_contents('signature.txt', json_encode($_REQUEST));

    if($signature == $tmpStr){
        return true;
    }else{
        return false;
    }
}

function AutoBack($postObj){
    //file_put_contents("WECHAT_COMBINE_COMMAND.txt", json_encode($postObj));
    if (strtolower($postObj->MsgType) == 'text') {
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time = time();
        $msgType = 'image';
        $mediaId = "2o_EqMfCk-_X6bNAydUVERCZ40EWjODWEglDmdr6z0Y";//unicode2utf8("\ue14c");
        $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
							<Image>
								<MediaId><![CDATA[%s]]></MediaId>
							</Image>
                            </xml>";
        $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $mediaId);
        echo $info;
    }
    if (strtolower($postObj->MsgType) == 'event') {
        //如果是关注 subscribe事件
        if (strtolower($postObj->Event) == 'subscribe') {
            $toUser = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $time = time();
            $msgType = 'text';
            //<a href="http://tinydream.antit.top/TinydreamWeb">首页</a>
            $content = "有梦想，坚持行动——欢迎加入追梦行动派！参加行动合约赢现金点击“"."<a href=\"http://".$GLOBALS['options']['combine_url']."/TinydreamWeb/vue/block/dist/clock.html\">行动打卡</a>"."”， 互助小梦想点击“"."<a href=\"http://".$GLOBALS['options']['combine_url']."/TinydreamWeb\">梦想互助</a>"."”，与行动派一起行动点击“"."<a href=\"http://".$GLOBALS['options']['combine_url']."/TinydreamWeb/html/cach.html\">立即进群</a>"."”。";
            //$content = "欢迎关注小梦想互助——互助小额零钱，夺大额梦想金！ 点击“"."<a href=\"http://".$GLOBALS['options']['combine_url']."/TinydreamWeb\">首页</a>"."”进入平台，了解小梦想互助玩法点击“"."<a href=\"http://".$GLOBALS['options']['combine_url']."/TinydreamWeb/html/question.html\">新手指引</a>"."”。";


            $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
            $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
            echo $info;
        }
    }
}

//微信公众号方法处理
$WebApp = [
    WECHAT_ACCESSTOKEN_COMMAND=>function(){
        $appid = $GLOBALS['options']['WEB_APP_ID'];
        $appsecret = $GLOBALS['options']['WEB_APP_SECRET'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";

        $output = PublicTools::https_request($url);
        $jsoninfo = json_decode($output, true);
        return $jsoninfo["access_token"];
    },
    WECHAT_COMBINE_COMMAND=>function(){
        $postArr = isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:"";
        $postObj = simplexml_load_string($postArr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if(!empty($postObj)){
            AutoBack($postObj);
            return;
        }
        if(CombineWechatServer()){
            file_put_contents('echostr.txt', $_REQUEST['echostr']);
            echo $_REQUEST['echostr'];
            return;
        }
    },
    WECHAT_GETUSERINFO_COMMAND =>function(){
        //echo json_encode($_REQUEST);
        Header("Location:https://".$GLOBALS['options']['combine_url']."/admin/demo.html?code=".$_REQUEST['code']."&state=".$_REQUEST['state']);
        //setcookie('code',json_encode($_REQUEST,JSON_UNESCAPED_UNICODE),PRC_TIME()+3600);
        return;
    },
    WECHAT_WEB_COMMAND=>function(){
        Header("Location:".$GLOBALS['options']['web_url']);
        return;
    },
    WECHAT_MENU_COMMAND=>function(){
        /*$appid = $GLOBALS['options']['WEB_APP_ID'];
        $appsecret = $GLOBALS['options']['WEB_APP_SECRET'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";

        $output = https_request($url);
        $jsoninfo = json_decode($output, true);
        $access_token = $jsoninfo["access_token"];*/
        $access_token = $GLOBALS['WebApp'][WECHAT_ACCESSTOKEN_COMMAND]();
        if($_REQUEST[WECHAT_MENU_COMMAND] == ''){
            //定义菜单的格式
            $jsonmenu = '{
                  "button":[
					   {
							"name":"梦想互助",
							"type":"view",
							"url":"'.$GLOBALS['options']['web_url'].'"
					   },{
							"name":"行动打卡",
							"type":"view",
							"url":"'.$GLOBALS['options']['web_url_operation'].'"
					   }
				   ]
             }';

        }else{
            $jsonmenu = $_REQUEST[WECHAT_MENU_COMMAND];
        }

        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $result = PublicTools::https_request($url, $jsonmenu);
        echo $result;
        return;
    },
    WECHAT_IMAGE_COMMAND=>function(){
        $access_token = $GLOBALS['WebApp'][WECHAT_ACCESSTOKEN_COMMAND]();
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=$access_token";
        $result = PublicTools::https_request($url,json_encode(['type'=>'image','offset'=>0,'count'=>10]));
        echo $result;
        return;
    },
    WECHAT_CUSTOMER_COMMAND=>function($content){
        $access_token = $GLOBALS['WebApp'][WECHAT_ACCESSTOKEN_COMMAND]();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$access_token";

        $msg = [
            'touser'=>'oSORf5kn6hr_H5ZSRyYSHFUzyBd4',
            'msgtype'=>"text",
            'text'=>[
                "content"=>$content
            ]
        ];

        $result = PublicTools::https_request($url,json_encode($msg,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        /*
        {
            "touser":"OPENID",
            "msgtype":"text",
            "text":
            {
                 "content":"Hello World"
            }
        }
        发送文本消
        */
        echo $result;
        return;
    },
	WECHAT_TEXT_COMMAND=>function($uid,$content){ 
		$access_token = $GLOBALS['WebApp'][WECHAT_ACCESSTOKEN_COMMAND]();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$access_token";

        $msg = [
            'touser'=>$uid,
            'msgtype'=>"text",
            'text'=>[
                "content"=>$content
            ]
        ];

        $result = PublicTools::https_request($url,json_encode($msg,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        echo $result;
        return;
	}
];

?>