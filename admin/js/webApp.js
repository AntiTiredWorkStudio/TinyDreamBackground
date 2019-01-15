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
  },
  InitUpload:function(){
      document.write('<script type="text/javascript" src="https://tinydream.antit.top/admin/js/qiniu.min.js"></script>');
  },
  UploadWithSDK :  function (token,domain,tfile,filename,OnQiniuComplete) {
		  var config = {
			  useCdnDomain: true,
			  disableStatisticsReport: false,
			  retryCount: 6,
			  region: qiniu.region.z0
		  };
		  var putExtra = {
			  fname: "",
			  params: {},
			  mimeType: null
		  };
		var file = tfile;
        var suffix = tfile.name.split(".")[1];
        var finishedAttr = [];
        var compareChunks = [];
        var observable;
        if (file) {
            var key = filename;
            putExtra.fname = key+"."+suffix;
           // console.log(putExtra["fname"] );
            putExtra.mimeType = ["image/png", "image/jpeg", "image/gif"];

            // 设置next,error,complete对应的操作，分别处理相应的进度信息，错误信息，以及完成后的操作
            var error = function(err) {
                console.log(err);
                //alert("上传出错");
            };

            var next = function(response) {
                var chunks = response.chunks||[];
                var total = response.total;
                // 这里对每个chunk更新进度，并记录已经更新好的避免重复更新，同时对未开始更新的跳过
                for (var i = 0; i < chunks.length; i++) {
                    if (chunks[i].percent === 0 || finishedAttr[i]){
                        continue;
                    }
                    if (compareChunks[i].percent === chunks[i].percent){
                        continue;
                    }
                    if (chunks[i].percent === 100){
                        finishedAttr[i] = true;
                    }
                }
                compareChunks = chunks;
            };

            var subObject = {
                next: next,
                error: error,
                complete: function(res){
                	if(res.hasOwnProperty("hash") && res.hasOwnProperty("key")) {
                        OnQiniuComplete({result:true,imgName:res.key});
                    }else{
                        OnQiniuComplete({result:false,msg:res});
					}
				}
            };
            var subscription;
            observable = qiniu.upload(file, key, token, putExtra, config);

            subscription = observable.subscribe(subObject);
        }
    },
	View:{
  		AddViewData:function (view,pars) {
  			var viewData = view.data;
            viewData.push(pars);
            view.data = viewData;
			return view;
        },
  		CreateSingleView:function(templateName,pars){
			if(pars == null){
				return {name:templateName,data:[]};
			}
			return {name:templateName,data:pars};
		},
		BuildsView:function(viewData,url,onCreated){
  			var requestData = {datas:JSON.stringify(viewData)};
  			if(url != null){
                requestData['url'] = url;
			}

  			TD_Request('view','builds',requestData,
				function(code,data){
					console.log(data);
					if(data.hasOwnProperty('snippet')) {

						for(var key in data.snippet){
                            var snippetStr = data.snippet[key];

                            var LB = new RegExp("#LB#","g");
							var RB = new RegExp("#RB#","g");

                            snippetStr = snippetStr.replace(LB,"<");
                            snippetStr = snippetStr.replace(RB,">");
                            data.snippet[key] = snippetStr;
						}
                        onCreated(true, data.snippet);
                    }else{
                        onCreated(false,data);
					}
				},
				function (code, data) {
                    console.log(data);
                    onCreated(false,data);
                }
  			)
			//未实现完成
			/*var str = "";
			str.replace("#LB#","<");
			str.replace("#RB#",">");*/
		}
	}
};
