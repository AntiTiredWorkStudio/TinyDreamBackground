<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */
	//echo $pageData['startTime'].'</br>';
	//echo $pageData['lastTime'].'</br>';
?>


<!-- 发布梦想池 class="main-content"-->
			<div  id='ReleaseDreamPool'>
				<!-- 输入框开始 -->
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">筛选条件</h3>
                        </div>
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">查询手机号</span>
                                        <input id="input_tele"  maxlength='11'  type="text" class="form-control" value="<?php echo $pageData['tele'];?>" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 10px"></div>
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">开始日期</span>
										<input id='startDayTime' size="16" type="text" value="<?php echo $pageData['startTime'];?>" readonly class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 10px"></div>
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">结束日期</span>
										<input id='endDayTime' size="16" type="text" value="<?php echo $pageData['lastTime'];?>" readonly class="form-control">
										<script type="text/javascript">
										</script>
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
							<h3 class="panel-title">订单列表： <span style="padding-left:25px;color:red;font-weight:bold;">总互助金额<?php echo $pageData['totalBill']."元";?></span></h3>
						</div>
						<div class="panel-body">
							<table class="table table-bordered ">
								<thead> 
									<tr> 
									 <th>订单号</th> 
									 <th>梦想互助期号</th> 
									 <th>互助金额（元）</th> 
									 <th>创建时间</th> 
									 <th>支付时间</th> 
									 <th>昵称</th> 
									 <th>手机号</th> 
									</tr> 
								   </thead> 
								   <tbody> 
									
								<?php
									//$verifyArray = $pageData['verify'];
									//$btnStyle = $pageData['btnStyle'];
									foreach($pageData['orders'] as $key=>$value){
										
								?>
								 <tr> 
									 <td><?php echo $value['oid'];?></td> 
									 <td><?php echo $value['pid'];?></td> 
									 <td><?php echo $value['bill'];?></td> 
									 <td><?php echo $value['ctime'];?></td> 
									 <td><?php echo $value['ptime'];?></td> 
									 <td><?php echo $value['nickname'];?></td> 
									 <td><?php echo $value['tele'];?></td> 
								 </tr> 
								<?php }?>
								   </tbody> 
							</table>
							<div class="desc" style="float: left;margin: 25px 0;">
								<p>每页显示<?php echo $pageData['size'];?>条记录，总计<?php echo $pageData['ordCount'];?>条记录 当前第<?php echo $pageData['index']['current']+1;?>页</p>
							</div>
							<div class="pagination" style="float: right;display: block;">
                                <?php if($pageData['index']['allowLast']){ ?>
                                <li>
									<a seek="<?php echo ($pageData['index']['current']-1)*$pageData['size'];?>" size="<?php echo $pageData['size'];?>" href="#">&laquo;</a>
								</li>
                                <?php }?>
                                <?php
								if(isset($pageData['index']['list'])){
                                $indexList = $pageData['index']['list'];
								$pageSeek = $pageData['index']['current'];
									foreach($indexList as $key=>$value) {
										?>
										<li>
											<a
												<?php if($key == $pageSeek){ ?>
												style="font-weight:bold;color:#d43f3a"
												<?php  }  ?>
												seek="<?php echo $value;?>" size="<?php echo $pageData['size'];?>" href="#"><?php echo ($key+1);?>
											</a>
										</li>
										<?php
									}
								}
                                ?>
                                <?php if($pageData['index']['allowNext']){ ?>
                                <li>
									<a seek="<?php echo ($pageData['index']['current']+1)*$pageData['size'];?>" size="<?php echo $pageData['size'];?>" href="#">&raquo;</a>
								</li>
                                <?php }?>
							</div>
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