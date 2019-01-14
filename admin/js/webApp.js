var WebApp = {
  Init:function(appid,complete){
	var app = this;
	if(app.GetAccessToken() == null){
		var codeData = app.GetAuthInfo();
		if(codeData==null){
			app.GetCode(appid);
		}else{
			TD_Request('us','gawt',
				{code:codeData.code},
				function(code,data){
					WebApp.GetUserInfo(
						function(result,data){
							if(complete){
								if(result){
									complete(result,JSON.parse(data));
								}else{
									console.log(result,data);
								}
							}
						}
					);
				},
				function(code,data){
					console.log(code,data);
				}
			);
		}
	}else{
		WebApp.GetUserInfo(
			function(result,data){
				//console.log(result,data);
				if(complete){
					if(result){
						complete(result,JSON.parse(data));
					}else{
						console.log(result,data);
					}
				}
			}
		);
	}
  },
  GetUserInfo:function(res){
	var accessTokenObject = JSON.parse(Options.AccessToken);
	var authObject = JSON.parse(Options.Auth);
	TD_Request('us','guif',
		{
			atoken:accessTokenObject.access_token,
			uid:accessTokenObject.openid
		},
		function(code,data){
			if(data.hasOwnProperty("info")){
				Options.UserInfo = data.info;
				res(true,data.info);
			}else{
				res(false,data);
			}
		},
		function(code,data){
			res(false,data);
		}
	);
  },
  GetAccessToken:function(){
	  //console.log(Options);
	  if(Options.hasOwnProperty("AccessToken") && Options.AccessToken != null){//获取过accesstoken
		  var accessTokenObject = JSON.parse(Options.AccessToken);
		  if(JSTimeToPHPTime(PRC_TIME()) > (accessTokenObject.timeStamp + accessTokenObject.expires_in)){//判断access token 失效
			  return null;
		  }
		  return Options.AccessToken; //判断access token 未失效
	  }
	  return null;
  },
  GetCode:function (web_appid) {
      var redirect = {
          appid:web_appid,
          redirect_uri:window.location.href,
          response_type:'code',
          scope:'snsapi_base',
          state:'1'
      }
      var url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="+redirect.appid+"&redirect_uri="+redirect.redirect_uri+"&response_type="+redirect.response_type+"&scope="+redirect.scope+"&state="+redirect.state+"#wechat_redirect";
        window.location.href = url;
  },
  GetAuthInfo:function(){
	var codeData = null;
	var getArr = $_GET;
	if(getArr['code']!=null && getArr['state']!=null){
		if(ExistStorage('code') && ExistStorage('state')){
			var tempCode = GetStorage('code');
			RemoveStorage('code');
			RemoveStorage('state');
			if(tempCode==getArr['code']){
				return null;
			}
		}
		codeData = {code:getArr['code'],state:getArr['state']};
		window.localStorage.setItem('code',getArr['code']);
		window.localStorage.setItem('state',getArr['state']);
	}
	return codeData;
  }
};
