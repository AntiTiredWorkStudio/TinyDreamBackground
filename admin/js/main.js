WebApp.InitUpload();

var main = function () {

    if(!HasLogin()){
        window.location.href= "index.html";
    }else {
        CheckAuthEnable(
            function (res) {
                if(res){
                    BuildNavigator();
                }else{
					Logout();
				}
            }
        );
    }
}

var Logout = function(){
    Options.Auth = null;
    SwitchPage('index.html');
}

var CheckAuthEnable = function(resfunc){
    TD_Request('auth','au',JSON.parse(Options.Auth),
        function (code, data) {
            resfunc(true);
        } ,
        function (code, data) {
            console.log(data);
            resfunc(false);
        }
    )
    //Options.Auth;
}


var BuildNavigator = function () {
    Page_Builder('admin','bnav',{},function (data) {
		$("#navigator").html(data);
    },function (code, data) {
        console.log(code, data);
    });
	
	Page_Builder('admin','pinfo',{uid:JSON.parse(Options.Auth).openid},function(data){
		$('#navbar-menu').html(data);
		$("#logout").click(Logout);
	},function(code,data){
		console.log(code,data);
	});
}

var SwitchTab = function (res) {
    //console.log(res.currentTarget.id);
    LoadWorkSpace(res.currentTarget.id,{});
}

var InitInput = function(){
	
}

var LoadWorkSpace = function (id,pars) {
    Page_Builder('admin',id,pars,
        function (data) {
            // console.log(data);
            $("#workspace").html(data);
        },function (code, data) {
            console.log(code, data);
        })
}
//创建页面控制器
document.OnPartLoad = function (data) {
	if(ModuleRegister.hasOwnProperty(data.id)){
		//console.log(ModuleRegister);
		ModuleRegister[data.id].init(data);
	}else{
		console.log("未定义控制器:"+data.id);
	}
}


var NavigatorModule = {
    init: function () {
//        $("#a_post").click(SwitchTab);
//        $("#a_verify").click(SwitchTab);
		var aList =  $('#navigator').find('a');
		//console.log(aList);
		aList.each(function(){
			console.log($(this));
			$(this).click(SwitchTab);
		});
		/*for(var key in aList){
			console.log(aList[key]);
		}*/
		//console.log($('a'));
    },
}

//梦想池管理模块
var PostModule = {
    init: function () {
		//tr_input_day,tr_input_tbill,tr_input_tbill,tr_input_ubill,tr_btn_post
        var module = this;
        $("[seek]").click(
            module.switchPage
        );
		$("#btn_post").click(
			module.postDreamPool
		);
		$("#tr_btn_post").click(
			module.postTradePool
		);
		$("#edit").click(
		    module.editDreamPool
        );
        $("#delete").click(
            module.deleteDreamPool
        );
		$("[tID]").click(
            module.onSelectTemplateID
		);
		this.postContent={
			input_Day:$("#input_day"),
			input_tBill:$("#input_tbill"),
			input_uBill:$("#input_ubill")
		}
		this.postTradeContent={
			//tr_input_day,tr_input_tbill,tr_input_tbill,tr_input_ubill
			input_Title:$("#tr_input_title"),
			input_tBill:$("#tr_input_tbill"),
			input_ID:$("#tr_input_id"),
			input_uBill:$("#tr_input_ubill")
		}
		console.log(this.postContent);
    },
	onSelectTemplateID:function(res){
		$("#tr_input_id").val($(res.currentTarget).attr('tid'));
	},
    editDreamPool:function (res) {
        var pid = $(res.currentTarget).attr('pid');

        str = ($(this).children($("span")).attr("class")=="lnr lnr-chevron-down")?"lnr lnr-pencil":"lnr lnr-chevron-down";
        console.log(str);
        $(this).val(str);   // 按钮被点击后，在“编辑”和“确定”之间切换

        $(this).children($("span")).attr("class", str);

        $(this).parent().siblings("td").each(
            function() {  // 获取当前行的其他单元格
                obj_text = $(this).find("input:text");    // 判断单元格下是否有文本框
                if (!obj_text.length)   // 如果没有文本框，则添加文本框使之可以编辑
                    $(this).html("<input class='edit_frame' type='text' value='" + $(this).text() + "'>");
                else   // 如果已经存在文本框，则将其显示为文本框修改的值
                    $(this).html(obj_text.val());
            }
        );

    },
    deleteDreamPool:function (res) {
        var pid = $(res.currentTarget).attr('pid');
    },
	postContent:null,
	postTradeContent:null,
	postTradePool:function(){
        var module = PostModule;
		console.log(module.postTradeContent.input_Title.val(),
		module.postTradeContent.input_tBill.val(),
		module.postTradeContent.input_ID.val(),
		module.postTradeContent.input_uBill.val());
		if(IsBill(module.postTradeContent.input_tBill.val())!=""){
			alert("目标金额不符合要求");
			return;
		}
		if(IsBill(module.postTradeContent.input_uBill.val())!=""){
			alert("单位金额不符合要求");
			return;
		}
		if(module.postTradeContent.input_Title.val()==""){
			alert("生意标题不符合要求");
			return;
		}
		if(module.postTradeContent.input_ID.val()==""){
			alert("生意详情模板id不符合要求");
			return;
		}
		TD_Request('tr','adt',
        {
			title:module.postTradeContent.input_Title.val(),
			url:module.postTradeContent.input_ID.val(),
			profit:module.postTradeContent.input_tBill.val()*100,
			ubill:module.postTradeContent.input_uBill.val()*100
			/*dblink: "test"测试代码*/
        },
        function(code,data){
			console.log(data);
            alert(data.context);
			LoadWorkSpace('a_post',{psize:5,seek:0});
        },
        function (code,data){
            console.log(code,data);
            alert(data.context);
        });
	},
	postDreamPool :function(){
        var module = PostModule;
		console.log(module.postContent.input_Day.val());
		if(IsDays(module.postContent.input_Day.val())!=""){
			alert("天数不符合要求:"+IsDays(module.postContent.input_Day.val()));
			return;
		}
		if(IsBill(module.postContent.input_tBill.val())!=""){
			alert("目标金额不符合要求");
			return;
		}
		
		if(IsBill(module.postContent.input_uBill.val())!=""){
			alert("单位金额不符合要求");
			return;
		}
//		'apbd'=>R('AddPoolByDay',["uid","tbill","ubill","day"]),
		TD_Request('dp','apbd',
        {
            uid:	"a01",
			tbill:	module.postContent.input_tBill.val()*100,
			ubill:	module.postContent.input_uBill.val()*100,
			day:	module.postContent.input_Day.val()
        },
        function(code,data){
			console.log(data);
            alert(data.context);
			LoadWorkSpace('a_post',{psize:5,seek:0});
        },
        function (code,data){;
            console.log(code,data);
            alert(data.context)
        }
    );
		
		
	},
    switchPage:function (page) {
        LoadWorkSpace('a_post',{psize:$(page.currentTarget).attr('size'),seek:$(page.currentTarget).attr('seek')});
    }
}

//认证审核模块
var VerfModule = {
    init: function (option) {
        console.log(option);
        this.verifyInfo = option;
        var module = this;
        $("#id_success").click(module.idVerify);
        $("#id_failed").click(module.idVerify);
        $("#dream_success").click(module.dreamVerify);
        $("#dream_failed").click(module.dreamVerify);
        $("#dream_payment").click(module.dreamPayment);
        $("#btn_type_submit").click(module.switchInfoType);
        $("#btn_type_unsubmit").click(module.switchInfoType);
        $("#btn_type_lose").click(module.switchInfoType);
    },
    verifyInfo:null,
	switchInfoType : function(res){
		console.log(res.currentTarget.id);
		switch(res.currentTarget.id){
			case "btn_type_submit":
				LoadWorkSpace('a_verify',{tab:'submit'});
				break;
			case "btn_type_unsubmit":
				LoadWorkSpace('a_verify',{tab:'unsubmit'});
				break;
			case "btn_type_lose":
				LoadWorkSpace('a_verify',{tab:'lose'});
				break;
			default:
				break;
		}
	},
    idVerify:function (res) {
        var tuid = $(res.currentTarget).attr('uid');
        var tstate = "";
        if(res.currentTarget.id == "id_success"){
            tstate = "SUCCESS";
        }else {
            tstate = "FAILED";
        }
        TD_Request('us','rnameax',{uid:tuid,state:tstate}
        ,function (code, data) {
                console.log(data);
                LoadWorkSpace('a_verify');
                alert(data.context);
            },
            function (code, data) {
                console.log(data);
                alert(data.context);
            }
        )
        console.log(res.currentTarget);
    },
    dreamVerify:function (res) {
        var tdid = $(res.currentTarget).attr('did');
        var result = "";
        if(res.currentTarget.id == "dream_success"){
            result = "SUCCESS";
        }else {
            result = "DOING";
        }

        TD_Request('dr','sdjson',{did:tdid,state:JSON.stringify({state:result})},
            function (code, data) {
                console.log(data);
                LoadWorkSpace('a_verify');
                alert(data.context);
            },
            function (code, data) {
                console.log(data);
                alert(data.context);
            }
        )
    },
    dreamPayment:function (res) {
        var tdid = $(res.currentTarget).attr('did');

        TD_Request('dr','sdjson',{did:tdid,state:JSON.stringify({payment:true})},
            function (code, data) {
                console.log(data);
                LoadWorkSpace('a_verify');
                alert(data.context);
            },
            function (code, data) {
                console.log(data);
                alert(data.context);
            }
        )
    }
}

var DataModule = {
	autoListID:[],
	init:function(option){
		console.log(option);
		var module = this;
		for(var key in option.recs){
			console.log(option.recs[key].date);
			$("#btn_"+option.recs[key].date).click(module.OnPersonCountView);
			module.autoListID.push("#btn_"+option.recs[key].date);
			
		}
		console.log(module.autoListID);
		this.AutoLoad();
	},
	AutoLoad:function(){
		var module = this;
		if(module.autoListID!=[]){
			var targetID = module.autoListID[0];
			if(!targetID){
				return;
			}
			
			var targetID = targetID.replace("#btn_","");
			console.log(targetID);
			module.GetPersonCount(targetID,function(id,result){
				if(result){
					module.autoListID.splice(0, 1);
					module.AutoLoad();
				}
			});
		}
	},
	GetPersonCount:function(id,result){
		TD_Request("ub","gad",
			{date:id},
			function(code,data){
				$("#day_visit_"+id).html(data.stat.visit);
				$("#day_join_"+id).html(data.stat.join);
				$("#day_paid_"+id).html(data.stat.paid);
				$("#day_btn_"+id).html("已加载");
				result(id,true);
			},
			function(code,data){
				console.log(data);
				result(id,false);
			}
		);
	},
	OnPersonCountView:function (res){
		console.log(res.currentTarget.id);
		var module = this;
		var targetID = res.currentTarget.id.replace("btn_","");
		DataModule.GetPersonCount(targetID,function(id,result){
			if(!result){
				alert("查看失败:"+data.context);
			}
		});
	}
}

var OrderModule = {
	seek:0,
	count:0,
	size:10,
	init:function(option){
		var module = this;
		console.log(option.ordCount,option.seek,option.count);
		console.log(option);
		this.seek = option.seek;
		this.count = option.count;
		$("#startDayTime").datetimepicker({
			minView: "month", //选择日期后，不会再跳转去选择时分秒 
			language:  'zh-CN',
			format: 'yyyy-mm-dd',
			todayBtn:  1,
			autoclose: 1,
		});
		$("#endDayTime").datetimepicker({
			minView: "month", //选择日期后，不会再跳转去选择时分秒 
			language:  'zh-CN',
			format: 'yyyy-mm-dd',
			todayBtn:  1,
			autoclose: 1,
		});
		$("#search").click(function(){
			/*console.log($("#input_tele").val());
			console.log($("#startDayTime").val());
			console.log($("#endDayTime").val());*/
			LoadWorkSpace('a_order',
			{
				tele:$("#input_tele").val(),
				startTime:$("#startDayTime").val(),
				endTime:$("#endDayTime").val()
			});
		});
		$("[seek]").click(
            module.switchPage
        );
	},
	switchPage:function(page){
		//console.log(page);
        LoadWorkSpace('a_order',
			{
				tele:$("#input_tele").val(),
				startTime:$("#startDayTime").val(),
				endTime:$("#endDayTime").val(),
				seek:$(page.currentTarget).attr('seek')
			}
		);
	}
}

var ActivityModule = {
	init:function(option){
		for(var key in option.act){
			$("#ok_"+option.act[key].pid).click(this.Upload);
		}
       // 
	},
	Upload:function(res){
		var tPid = $(res.currentTarget).attr('pid');
		
		console.log("Upload",$("#file_"+tPid)[0].files[0]);
		var targetFile = $("#file_"+tPid)[0].files[0];
		if(targetFile == null){
			alert("未选择文件");
			return;
		}
		TD_Request("aw","astart",{pid:tPid},
			function(code,data){
				var realImgUrl = data.token.domain+"/"+data.token.fileName;
				 WebApp.UploadWithSDK(data.token.uptoken, data.token.upurl,targetFile,data.token.fileName,
                    function(result)
                    {
						if(result.result){
							TD_Request("aw","aend",{pid:tPid,url:realImgUrl},
								function(code,data){
									alert("上传成功!");
									LoadWorkSpace('a_activity');
								},function(code,data){
									alert(data.context);
								}
							);
						}else{
							alert(JSON.stringify(result));
						}
					});
			},
			function(code,data){
				console.log(data);
				alert(data.context);
			}
		);
	}
}

var RefundModule = {
	init :function(option){
		var module = this;
		console.log(option);
		for(var key in option.packs.refund){
			$("#refund_"+key).click(this.refundFunc);
		}
		$("[pid]").click(
            module.onSelectPid
		);
		$("#search").click(function(){
			/*console.log($("#input_tele").val());
			console.log($("#startDayTime").val());
			console.log($("#endDayTime").val());*/
			if($("#input_pid").val() == ""){
				alert("请输入要查询的梦想互助期号");
				return;
			}
			LoadWorkSpace('a_refund',
			{
				pid:$("#input_pid").val()
			});
		});
	},
	onSelectPid:function(res){
		$("#input_pid").val($(res.currentTarget).attr('pid'));
	},
	refundFunc:function(res){
		var tRid = $(res.currentTarget).attr('rid');
		var tPid = $(res.currentTarget).attr('pid');
		console.log(tRid,tPid);
		TD_Request("rp","drefund",{rid:tRid,pid:tPid},
			function(code,data){
				alert(data.context);
				$(res.currentTarget).hide();
			},
			function(code,data){
				alert(data.context);
			}
		);
	}
}

var RedPackageModule = {
	seek:0,
	count:0,
	size:10,
	pid:"",
	init:function(option){
		var module = this;
		console.log("init:",option);
		//this.seek = option.seek;
		//this.count = option.count;
		if(option.hasOwnProperty("packs") && option.packs.hasOwnProperty("pid")){
			module.pid = option.packs.pid;
		}
		$("[pid]").click(
            module.onSelectPid
		);
		$("#search").click(function(){
			/*console.log($("#input_tele").val());
			console.log($("#startDayTime").val());
			console.log($("#endDayTime").val());*/
			//alert($("#input_pid").val());
			LoadWorkSpace('a_redpack',
			{
				pid:$("#input_pid").val(),
				seek:module.seek,
				count:module.size,
				type:"listview"
			});
		});
		$("[seek]").click(
            module.switchPage
        );
	},
	onSelectPid:function(res){
		$("#input_pid").val($(res.currentTarget).attr('pid'));
	},
	switchPage:function(page){
		console.log(RedPackageModule.seek);
		//console.log(page.currentTarget.attributes[0].value,page.currentTarget.attributes[1].value);
		//return;
		//var module = this;
			LoadWorkSpace('a_redpack',
			{
				pid:RedPackageModule.pid,
				seek:page.currentTarget.attributes[0].value,
				count:page.currentTarget.attributes[1].value,
				type:"listview",
			});
	}
}

var OperationModule = {
	seek:0,
	count:5,
	size:10,
	state:"ALL",
	init:function(option){
		var module = this;
		if(ExistStorage("tele")){
			$("#input_tele").val(GetStorage("tele"));
		}
		console.log("init:",option);
		$("#ALL").click(function(){
			OperationModule.state = "ALL";
			LoadWorkSpace('a_operation',
			OperationModule.onGetArgs());
		});
		$("#DOING").click(function(){
			OperationModule.state = "DOING";
			LoadWorkSpace('a_operation',
			OperationModule.onGetArgs());
		});
		$("#SUCCESS").click(function(){
			OperationModule.state = "SUCCESS";
			LoadWorkSpace('a_operation',
			OperationModule.onGetArgs());
		});
		$("#FAILED").click(function(){
			OperationModule.state = "FAILED";
			LoadWorkSpace('a_operation',
			OperationModule.onGetArgs());
		});
		$("[seek]").click(
            module.switchPage
        );
	},
	onGetArgs:function(arg){
		if(arg == null){
			arg = {
				state:OperationModule.state,
				seek:OperationModule.seek,
				count:OperationModule.count,
			};
		}
		if($("#input_tele").val()!=""){
			SaveStorage("tele",$("#input_tele").val());
			arg.tele = $("#input_tele").val();
		}else{
			RemoveStorage('tele');
		}
		return arg; 
	},
	switchPage:function(page){
		console.log(OperationModule.seek);
		//console.log(page.currentTarget.attributes[0].value,page.currentTarget.attributes[1].value);
		//return;
		//var module = this;
			
			LoadWorkSpace('a_operation',
			OperationModule.onGetArgs({
				state:OperationModule.state,
				seek:page.currentTarget.attributes[0].value,
				count:page.currentTarget.attributes[1].value,
			}));
	}
}

var ToolsModule = {
	seek:0,
	count:5,
	size:10,
	init:function(option){
		var module = this;
		$("[seek]").click(
            module.switchPage
        );
		$("#btn_submit").click(
			module.onPostTools
		);
	},
	onPostTools:function(page){
		console.log(
			$("#title").val(),
			$("#icon").val(),
			$("#qrcode").val(),
			$("#description").val(),
			$("#type").val(),
		);
		TD_Request("co","apa",{
			title:$("#title").val(),
			icon:$("#icon").val(),
			qrcode:$("#qrcode").val(),
			description:$("#description").val(),
			type:$("#type").val()
		},
			function(code,data){
				alert(data.context);
					LoadWorkSpace('a_tools',
				ToolsModule.onGetArgs({
					seek:0,
					count:5,
				}));
			},
			function(code,data){
				alert(data.context);
			}
		);
		
		console.log(page);
	},
	onGetArgs:function(arg){
		if(arg==null)
			arg = {
				seek:ToolsModule.seek,
				count:ToolsModule.count,
			};
		return arg; 
	},
	switchPage:function(page){
		console.log(OperationModule.seek);
			
			LoadWorkSpace('a_tools',
			ToolsModule.onGetArgs({
				seek:page.currentTarget.attributes[0].value,
				count:page.currentTarget.attributes[1].value,
			}));
	}
}

var FilesModule = {
	seek:0,
	count:5,
	size:10,
	init:function(option){
		var module = this;
		$("[seek]").click(
            module.switchPage
        );
		$("#btn_submit").click(
			module.onPostTools
		);
		module.UploadObject.Init();
	},
	onPostTools:function(page){
	},
	onGetArgs:function(arg){
		if(arg==null)
			arg = {
				seek:FilesModule.seek,
				count:FilesModule.count,
			};
		return arg; 
	},
	switchPage:function(page){
		console.log(OperationModule.seek);
			
			LoadWorkSpace('a_file',
			ToolsModule.onGetArgs({
				seek:page.currentTarget.attributes[0].value,
				count:page.currentTarget.attributes[1].value,
			}));
	},
	UploadObject:{
		fileList:[],
		idArray:[],
		fileArray:[],
		tokens:{},
		resultArray:[],
		Init:function(){
			$("#uploader").change(function (res) {
				FilesModule.UploadObject.fileList = res.target.files;
			})
			$('#btn_submit').click(function(res){
				for(var i in FilesModule.UploadObject.fileList){
					if(isNaN(i)){
						continue;
					}
					FilesModule.UploadObject.idArray.push(FilesModule.UploadObject.fileList[i].name);
					FilesModule.UploadObject.fileArray.push(FilesModule.UploadObject.fileList[i]);
				}
				WebApp.GenerateTokens(
					FilesModule.UploadObject.idArray,"icon",
					function(result,token){
						console.log(token);
						FilesModule.UploadObject.tokens = token;
						FilesModule.UploadObject.InitUploadQueue();
					}
				);
			});
		},
		InitUploadQueue:function(){
			if(FilesModule.UploadObject.idArray == [] || FilesModule.UploadObject.fileArray == []){
				console.log(FilesModule.UploadObject.resultArray);
				return;
			}
			var file = FilesModule.UploadObject.fileArray.shift();
			if(file==null){
				FilesModule.UploadObject.UploadFinished();
				console.log(FilesModule.UploadObject.resultArray);
				return;
			}
			var token = FilesModule.UploadObject.tokens[file.name];
			var url_prefix = token.domain;
			console.log("准备上传:"+JSON.stringify(token));
			WebApp.UploadWithSDK(token.uptoken, token.upurl,file,token.fileName,function(result){
				console.log(result.imgName);
				result.imgName = url_prefix+"/"+result.imgName;
				FilesModule.UploadObject.resultArray.push(result.imgName);
				FilesModule.UploadObject.InitUploadQueue();
			});
		},
		UploadFinished:function(){
			TD_Request('view','upload_img',{imglist:JSON.stringify(FilesModule.UploadObject.resultArray)},function(code,data){
				console.log(data);
			},
			function(code,data){
				console.log(data);
			});
		}
	}
}



var ModuleRegister = {
	"bnav":NavigatorModule,
	"a_post":PostModule,
	"a_verify":VerfModule,
	"a_data":DataModule,
	"a_order":OrderModule,
	"a_activity":ActivityModule,
	"a_refund":RefundModule,
	"a_redpack":RedPackageModule,
	'a_operation':OperationModule,
	'a_tools':ToolsModule,
	'a_file':FilesModule
}

Page.OnSignalFailed = function () {
    window.location.href = "index.html";
}

main();