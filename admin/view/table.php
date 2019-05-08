
						<div class="panel-body">
							<table class="table table-bordered ">
								<thead> 
									<tr> 
									<?php
									if(isset($pageData['fields'])){
										foreach($pageData['fields'] as $field){
									?>
									 <th><?php echo $field;?></th> 
									<?php
										}
									}
									?>
									</tr> 
								   </thead> 
								   <tbody> 
									
								<?php
								
								if(isset($pageData['data'])){
									foreach($pageData['data'] as $key=>$value){
								?>
								 <tr> 
									<?php
									foreach($value as $k=>$v){
									?>
									 <td><?php echo $v;?></td> 
									<?php
									}
									?>
								 </tr> 
								<?php }
								}
								?>
								   </tbody> 
							</table>
						</div>
<?php
						if(isset($pageData['index'])){
								?>
							<div class="desc" style="float: left;margin: 25px 0;">
								<p>每页显示<?php echo $pageData['index']['size'];?>条记录，总计<?php echo $pageData['index']['count'];?>条记录 当前第<?php echo $pageData['index']['current']+1;?>页</p>
							</div>
							<div class="pagination" style="float: right;display: block;">
                                <?php if($pageData['index']['allowLast']){ ?>
                                <li>
									<a seek="<?php echo ($pageData['index']['current']-1)*$pageData['index']['size'];?>" size="<?php echo $pageData['index']['size'];?>" href="#">&laquo;</a>
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
												seek="<?php echo $value;?>" size="<?php echo $pageData['index']['size'];?>"
												<?php if($key == $pageSeek){ ?>
												style="font-weight:bold;color:#d43f3a"
												<?php  }  ?>
												href="#"><?php echo ($key+1);?>
											</a>
										</li>
										<?php
									}
								}
                                ?>
                                <?php if($pageData['index']['allowNext']){ ?>
                                <li>
									<a seek="<?php echo ($pageData['index']['current']+1)*$pageData['index']['size'];?>" size="<?php echo $pageData['index']['size'];?>" href="#">&raquo;</a>
								</li>
                                <?php }?>
							</div>
					<?php
						}
					?>