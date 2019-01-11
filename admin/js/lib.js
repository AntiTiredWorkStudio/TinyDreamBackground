
var Options = {
    Url : "https://tinydream.antit.top",//http://localhost:8003 , https://tinydream.antit.top
    Auth:null
};
var Page = {
    OnSignalFailed : function (data) {

    }
};
var SwitchPage = function (url) {
    /*if(Options.Auth!=null) {
        SaveStorage("auth", Options.Auth);
    }*/
    window.location.href = url;
}

var CheckAndSetAuthInfo = function (requestData) {
    if(requestData.hasOwnProperty('auth') && requestData.hasOwnProperty("openid")) {
        var authData = requestData.auth;
        authData.openid = requestData.openid;
        Options.Auth = JSON.stringify(authData);
    }
}

var Page_Builder = function (module, action, paras, fResult,fFailed) {
    var postInfo = {};
    postInfo[module] = action;

    for(var k in paras){
        postInfo[k] = paras[k];
    }

    if(Options.Auth != null){
        var auth = JSON.parse(Options.Auth);
        var secret = auth.secret;
        var openid = auth.openid;
        var timeStamp = auth.timeStamp;
        if(!postInfo.hasOwnProperty('uid')){
            postInfo['openid'] = openid;
        }
        var signal = GetSignalString(secret,timeStamp,postInfo);
        postInfo['signal'] = signal;
    }

    var ajaxObject ={
        url: Options.Url,
        type: "post",
        dataType: "html",
        data: postInfo,
        success: function (data) {
            try {
                if (JSON.parse(data).code == 97) {
                    Page.OnSignalFailed(data);
                    fResult(data);
                } else {
                    CheckAndSetAuthInfo(data);
                    fResult(data);
                }
            }catch (err){
                CheckAndSetAuthInfo(data);
                fResult(data);
            }
        },
        error: function (e) {
            if(e.status=='200'){
                fResult(e.responseText);
            }else {
                fFailed('-1', e);
            }
        }
    };
    $.ajax(ajaxObject);
}

var GetSignalString = function(secret,timeStamp,requestArray){
    var requestString = '';
    for(var key in requestArray){
        requestString = requestString + key +"="+requestArray[key]+"&";
    }
    return sha1(requestString.substring(0, requestString.length - 1)+"&secret="+secret+"&time="+timeStamp);
}

//后台通用请求模板
var TD_Request = function(module,action,paras,fSuccess,fFailed) {

    var postInfo = {};
    postInfo[module] = action;

    for(var k in paras){
        postInfo[k] = paras[k];
    }
    //console.log(postInfo);
    //console.log(GetSignalString(postInfo));
    //console.log(window.localStorage.getItem("auth"));
	if(Options.Auth != null){
		var auth = JSON.parse(Options.Auth);
		var secret = auth.secret;
		var openid = auth.openid;
		var timeStamp = auth.timeStamp;
		if(!postInfo.hasOwnProperty('uid')){
		    postInfo['openid'] = openid;
        }
		var signal = GetSignalString(secret,timeStamp,postInfo);
		postInfo['signal'] = signal;
	}

    var ajaxObject ={
        url: Options.Url,
        type: "post",
        dataType: "json",
        data: postInfo,
        success: function (data) {
            CheckAndSetAuthInfo(data);
            if(data["result"] == "true" || data['code']=="0"){
                fSuccess(data['code'],data);
            }else{
                if(data['code'] == 97){
                    Page.OnSignalFailed(data);
                }else {
                    CheckAndSetAuthInfo(data);
                }
                fFailed(data['code'],data);
            }
        },
        error: function (e) {
            fFailed('-1',e);
        }
    };
    $.ajax(ajaxObject);
}

//http get请求
var Http_Get_Request = function(url,datas,fSuccess,fFailed) {

    var ajaxObject ={
        url: url,
        type: "get",
        dataType: "json",
        data: datas,
        success: fSuccess,
        error: fFailed
    };
    $.ajax(ajaxObject);
}

//检查验证码格式
var IsValidateCode = function(target){

    if(target == null || target==""){
        return "还未输入验证码";
    }

    if(!(/^\d{6}$/.test(target))){
        return "验证码格式有误，请重填";
    }

    return "";
}

//检查天数格式
var IsDays = function(target){

    if(target == null || target==""){
        return "还未输入天数";
    }

    if(!(/[1-99]\d|\d/.test(target))){
        return "天数格式有误，请重填";
    }

    return "";
}


//检查金额格式
var IsBill = function(target){

    if(target == null || target==""){
        return "还未输入金额";
    }

    if(!(/[1-10000000]\d|\d/.test(target))){
        return "金额格式有误，请重填";
    }

    return "";
}

//检查手机号格式
var IsCellPhoneNumber = function(target){
    if(target == null || target==""){
        return "还未输入手机号码";
    }

    if(!(/^1[34578]\d{9}$/.test(target))){
        return "手机号码有误，请重填";
    }

    return "";
}

var SaveStorage = function (key,value) {
    window.localStorage.setItem(key,value);
}

var RemoveStorage = function (key) {
    window.localStorage.removeItem(key);
}

var ExistStorage = function(key){
    return !(window.localStorage.getItem(key)==null);
}

var GetStorage = function(key){
    return window.localStorage.getItem(key);
}

var PRC_TIME = function () {
    return (new Date()).getTime();// + 8*3600000;
}

//php 时间戳转换为js时间戳
var PhpTimeToJsTime = function (time) {
    return (time - 8*3600)*1000;
}

//js 时间戳转换为php时间戳
var JSTimeToPHPTime = function (time) {
    return Math.floor(time/1000);
}

var DescriptionTime = function(sec) {
    if (sec < 60) {
        return sec + "秒";
    }
    if (sec < 3600) {
        return Math.floor(sec / 60) + "分钟";
    }
    if (sec < 86400) {
        return Math.floor(sec / 3600) + "小时";
    }

    if (sec < 86400 * 30) {
        return Math.floor(sec / 86400) + "天";
    }
}

var formatNumber = function (n) {
    n = n.toString()
    return n[1] ? n : '0' + n
}

var GetLocalTime = function (number) {


    console.log(number);
    var date = new Date(number * 1000);
    //年
    var Y = date.getFullYear();
    //月
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
    //日
    var D = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    //时
    var h = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
    //分
    var m = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
    //秒
    var s = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();

    return Y+"-"+M+'-'+D+ '  '+h+':'+m+':'+s ;
}

var BillExchange = function(bill) {
    var result = {
        value: 0,
        unit: ""
    }

    if (bill < 1000000) {
        result.value = bill * 0.01;

        result.value = Math.floor(result.value * 100) / 100

        result.unit = "元";
        return result;
    } else {
        result.value = bill * 0.000001;

        result.value = Math.floor(result.value * 100) / 100

        result.unit = "万元";
        return result;
    }
    return result;
}

var encodeUTF8 = function(s) {
    var i, r = [],
        c, x;
    for (i = 0; i < s.length; i++)
        if ((c = s.charCodeAt(i)) < 0x80) r.push(c);
        else if (c < 0x800) r.push(0xC0 + (c >> 6 & 0x1F), 0x80 + (c & 0x3F));
        else {
            if ((x = c ^ 0xD800) >> 10 == 0) //对四字节UTF-16转换为Unicode
                c = (x << 10) + (s.charCodeAt(++i) ^ 0xDC00) + 0x10000,
                    r.push(0xF0 + (c >> 18 & 0x7), 0x80 + (c >> 12 & 0x3F));
            else r.push(0xE0 + (c >> 12 & 0xF));
            r.push(0x80 + (c >> 6 & 0x3F), 0x80 + (c & 0x3F));
        };
    return r;
}

var sha1 = function(s) {
    var data = new Uint8Array(encodeUTF8(s))
    var i, j, t;
    var l = ((data.length + 8) >>> 6 << 4) + 16,
        s = new Uint8Array(l << 2);
    s.set(new Uint8Array(data.buffer)), s = new Uint32Array(s.buffer);
    for (t = new DataView(s.buffer), i = 0; i < l; i++) s[i] = t.getUint32(i << 2);
    s[data.length >> 2] |= 0x80 << (24 - (data.length & 3) * 8);
    s[l - 1] = data.length << 3;
    var w = [],
        f = [
            function() {
                return m[1] & m[2] | ~m[1] & m[3];
            },
            function() {
                return m[1] ^ m[2] ^ m[3];
            },
            function() {
                return m[1] & m[2] | m[1] & m[3] | m[2] & m[3];
            },
            function() {
                return m[1] ^ m[2] ^ m[3];
            }
        ],
        rol = function(n, c) {
            return n << c | n >>> (32 - c);
        },
        k = [1518500249, 1859775393, -1894007588, -899497514],
        m = [1732584193, -271733879, null, null, -1009589776];
    m[2] = ~m[0], m[3] = ~m[1];
    for (i = 0; i < s.length; i += 16) {
        var o = m.slice(0);
        for (j = 0; j < 80; j++)
            w[j] = j < 16 ? s[i + j] : rol(w[j - 3] ^ w[j - 8] ^ w[j - 14] ^ w[j - 16], 1),
                t = rol(m[0], 5) + f[j / 20 | 0]() + m[4] + w[j] + k[j / 20 | 0] | 0,
                m[1] = rol(m[1], 30), m.pop(), m.unshift(t);
        for (j = 0; j < 5; j++) m[j] = m[j] + o[j] | 0;
    };
    t = new DataView(new Uint32Array(m).buffer);
    for (var i = 0; i < 5; i++) m[i] = t.getUint32(i << 2);

    var hex = Array.prototype.map.call(new Uint8Array(new Uint32Array(m).buffer), function(e) {
        return (e < 16 ? "0" : "") + e.toString(16);
    }).join("");

    return hex;
}

window.onbeforeunload = function (e) {
    if(Options.Auth!=null) {
        SaveStorage("auth", Options.Auth);
    }
};

var HasLogin = function () {
    return Options.Auth!=null;
}

var WebApp = {
  GetCode:function (web_appid,complete) {
      var url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=1#wechat_redirect";
    Http_Get_Request(
        'https://open.weixin.qq.com/connect/oauth2/authorize',
        {
            appid:web_appid,
            redirect_uri:'https://tinydream.antit.top/demo.html',
            response_type:'code',
            scope:'SCOPE',
            state:'1#wechat_redirect'
        },
        function (res) {
            complete(res);
            console.log(res);
        },
        function (res) {
            complete(res);
            console.log(res);
        }
    )
  }  
};

var InitOptions = function () {
    if(ExistStorage("auth")){
        var auth = GetStorage("auth");
        RemoveStorage("auth");
        Options.Auth = auth;
        console.log('init auth');
    }
}
InitOptions();