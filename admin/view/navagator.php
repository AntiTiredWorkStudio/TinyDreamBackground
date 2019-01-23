<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:40
 */
    $navigators = $pageData['navList'];

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
