<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */
?>
<h1>中奖未上传活动照片项</h1>
  <table class="table table-bordered "> 
   <thead> 
    <tr> 
     <th>期号</th> 
     <th>活动照片</th> 
     <th>中奖编号</th> 
     <th>图片选择</th>
	 <th>上传</th>
    </tr> 
   </thead> 
   <tbody> 
    
<?php
    //$verifyArray = $pageData['verify'];
    //$btnStyle = $pageData['btnStyle'];
	foreach($pageData['act'] as $key=>$value){
		
?>
 <tr> 
     <td><?php echo "梦想互助".$value['pid'].'期';?></td> 
     <td>
	 <?php
		if($value['imgurl']!=""){
	 ?>
		<a href="<?php echo $value['imgurl'];?>">点击查看</a>
	<?php
		}else{
			echo "未上传";
		}
	?>
	</td> 
     <td><?php echo $value['lid'];?></td> 
     <td><input id="file_<?php echo $value['pid'];?>" type="file" pid="<?php echo $value['pid'];?>" accept="image/*"></input></td> 
	 <td><button id="ok_<?php echo $value['pid'];?>" type="button" pid="<?php echo $value['pid'];?>" class="btn btn-success">上传</button></td> 
 </tr> 
<?php }?>
   </tbody> 
  </table>
<div style="padding-bottom:100px"></div>
<script>
    if(document.OnPartLoad){
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>