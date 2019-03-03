<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */
	//echo json_encode($pageData).'</br>';
	//echo $pageData['lastTime'].'</br>';
?>


<!-- 发布梦想池 class="main-content"-->
			<div  id='ReleaseDreamPool'>
				<!-- 输入框开始 -->
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">梦想互助查询</h3>
                        </div>
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">梦想互助期号</span>
                                        <input id="input_pid"  maxlength='11'  type="text" class="form-control" value="" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-7" style="margin: 15px auto;display: block;text-align: center;">
                                <button id="search" type="button" class="btn btn-primary" style="width: 10%;">查询</button>
                            </div>
                        </div>
                    </div>
                </div>
				<!-- 输入框结束 -->
				<div class="col-md-12">
					<!-- BASIC TABLE -->
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">退款需求列表：<span style="padding-left:25px;color:red;font-weight:bold;">梦想互助<?php echo $pageData['packs']['pid']; ?>期</span></h3>
						</div>
						<div class="panel-body">
							<table class="table table-bordered ">
								<thead> 
									<tr> 
									 <th>红包id</th> 
									 <th>发出人</th> 
									 <th>手机号</th> 
									 <th>单价(元)</th> 
									 <th>领取状态</th> 
									 <th>未领份数</th> 
									 <th>应该退金额(元)</th> 
									</tr> 
								   </thead> 
								   <tbody> 
									
								<?php
									//$verifyArray = $pageData['verify'];
									//$btnStyle = $pageData['btnStyle'];
									foreach($pageData['packs']['refund'] as $key=>$value){
										
								?>
								 <tr> 
									 <td><?php echo $key;?></td> 
									 <td><?php echo $value['nickname'];?></td> 
									 <td><?php echo $value['tele'];?></td> 
									 <td><?php echo $value['unit']*0.01;?></td> 
									 <td><?php echo $value['rcount'].'/'.$value['gcount'];?></td> 
									 <td><?php echo $value['less'];?></td> 
									 <td><?php echo $value['lbill']*0.01;?></td> 
								 </tr> 
								<?php }?>
								   </tbody> 
							</table>
						</div>

					</div>
					<!-- END BASIC TABLE -->
				</div>
			</div>
<script>
    if(document.OnPartLoad) {
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>