<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */
	//echo json_encode($pageData).'</br>';
?>


<!-- 发布梦想池 class="main-content"-->
			<div  id='ReleaseDreamPool'>
				<!-- 输入框开始 -->
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">按状态查询行动</h3>
                        </div>
                        <div class="panel-body">
							<div class="row">
								<div class="col-lg-7" style="margin: 15px auto;display: block;text-align: center;">
									<span><button id="ALL" type="button" class="btn btn-primary" style="width: 10%;">所有</button></span>
									<span><button id="DOING" type="button" class="btn btn-primary" style="width: 10%;">进行中</button></span>
									<span><button id="SUCCESS" type="button" class="btn btn-primary" style="width: 10%;">完成</button></span>
									<span><button id="FAILED" type="button" class="btn btn-primary" style="width: 10%;">失败</button></span>
								</div>
							</div>
                        </div>
                    </div>
                </div>
				<!-- 输入框结束 -->
				<div class="col-md-12">
					<!-- BASIC TABLE -->
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">行动列表:</h3>
						</div>
						<div class="panel-body">
							<table class="table table-bordered ">
								<thead> 
									<tr> 
									 <th>行动id</th> 
									 <th>用户</th> 
									 <th>行动类型</th> 
									 <th>行动开始时间</th> 
									 <th>上次打卡时间</th> 
									 <th>行动主题</th> 
									 <th>已打卡天数</th> 
									 <th>连续打卡天数</th> 
									 <th>缺失天数</th> 
									 <th>补卡天数</th> 
									 <th>补卡机会</th> 
									 <th>邀请用户</th> 
									 <th>行动状态</th> 
									</tr> 
								   </thead> 
								   <tbody> 
									
								<?php
								
								if(isset($pageData['operations']['data'])){
									foreach($pageData['operations']['data'] as $key=>$value){
								?>
								 <tr> 
									 <td><?php echo $value['opid'];?></td> 
									 <td><?php echo $value['uid'];?></td> 
									 <td><?php echo $value['cid'];?></td> 
									 <td><?php echo $value['starttime'];?></td> 
									 <td><?php echo $value['lasttime'];?></td> 
									 <td><?php echo $value['theme'];?></td> 
									 <td><?php echo $value['alrday'];?></td> 
									 <td><?php echo $value['conday'];?></td> 
									 <td><?php echo $value['misday'];?></td> 
									 <td><?php echo $value['menday'];?></td> 
									 <td><?php echo $value['menchance'];?></td> 
									 <td><?php echo $value['invcount'];?></td> 
									 <td><?php echo $value['state'];?></td> 
								 </tr> 
								<?php }
								}
								?>
								   </tbody> 
							</table>
						</div>
					
					<!-- 页面切换导航 -->
					<?php include_once("admin/view/indexlist.php");?>
					<!-- 页面切换导航 -->
					</div>
					<!-- END BASIC TABLE -->
				</div>
			</div>
<script>
    if(document.OnPartLoad) {
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>