<p>这是一个模板文件</p>

<p><?php echo $this->value['name']; ?></p>


<?php
    echo date('Y-m-d H-i:s');
?>


<?php foreach((array)$this->value['arr'] as $k=>$v){ ?>
<br/>
<?php echo $k; ?>---<?php echo $v; ?>
<?php } ?>


<?php if( $condition == TRUE){ ?>
真
<?php }else{ ?>
假
<?php } ?>