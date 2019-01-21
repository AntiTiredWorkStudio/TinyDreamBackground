<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:40
 */
    $navigators = [
        'a_post'=>['title'=>'发布梦想池','class'=>'lnr lnr-home'],
        'a_verify'=>['title'=>'中标用户审核','class'=>'lnr lnr-pencil'],
		'a_data'=>['title'=>'访问数据统计','class'=>'lnr lnr-dice'],
    ];
    foreach ($navigators as $key=>$value) {
?>
        <li>
            <a id="<?php echo $key;?>" href="#" class="">
                <i class="<?php echo $value['class'];?>"></i><span><?php echo $value['title'];?></span>
            </a>
        </li>
<?php
    }
?>
<script>
    if(document.OnPartLoad) {
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>
