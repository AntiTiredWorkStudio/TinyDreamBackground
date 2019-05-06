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
								
								if(isset($pageData['packs']['redpack'])){
									//$verifyArray = $pageData['verify'];
									//$btnStyle = $pageData['btnStyle'];
									foreach($pageData['packs']['redpack'] as $key=>$value){
										
								?>
								 <tr> 
									 <td><?php echo $value['rid'];?></td> 
									 <td><?php echo $value['nickname'];?></td> 
									 <td><?php echo $value['tele'];?></td> 
									 <td><?php echo $value['bill']*0.01;?></td> 
									 <td><?php echo $value['bill']/$value['rcount']*0.01;?></td> 
									 <td><?php echo $value['rcount'].'/'.$value['gcount'];?></td> 
									 <td><?php echo date('Y-m-d H:i:s', $value['ctime']);?></td> 
									 <td><?php echo $value['content'];?></td> 
									 <td><?php echo $value['pid'];?></td> 
								 </tr> 
								<?php }
								}
								?>
								   </tbody> 
							</table>
						</div>
					
					<!-- 页面切换导航 -->
					<?php
						if(isset($pageData['packs']['totalpage'])){
								?>
							<div class="desc" style="float: left;margin: 25px 0;">
								<p>每页显示<?php echo $pageData['packs']['size'];?>条记录，总计<?php echo $pageData['packs']['total'];?>条记录 当前第<?php echo $pageData['packs']['currentpage']+1;?>页</p>
							</div>
							<div class="pagination" style="float: right;display: block;">
                                <?php if($pageData['index']['allowLast']){ ?>
                                <li>
									<a seek="<?php echo ($pageData['index']['current']-1)*$pageData['packs']['size'];?>" size="<?php echo $pageData['packs']['size'];?>" href="#">&laquo;</a>
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
												seek="<?php echo $value;?>" size="<?php echo $pageData['packs']['size'];?>" href="#"><?php echo ($key+1);?>
											</a>
										</li>
										<?php
									}
								}
                                ?>
                                <?php if($pageData['index']['allowNext']){ ?>
                                <li>
									<a seek="<?php echo ($pageData['index']['current']+1)*$pageData['packs']['size'];?>" size="<?php echo $pageData['packs']['size'];?>" href="#">&raquo;</a>
								</li>
                                <?php }?>
							</div>
					<?php
						}
					?>
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