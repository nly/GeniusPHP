<script type="text/javascript" src="http://cdn.staticfile.org/jquery/2.1.0/jquery.min.js?t=1391767420"></script>
<link type="text/css" rel="stylesheet" href="http://cdn.staticfile.org/meyer-reset/2.0/reset.min.css?t=1391767420" />
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