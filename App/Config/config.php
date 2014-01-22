<?php

/* 数据库配置 */
$CONFIG['system']['db'] = array(
    'database_type' =>  'mysql',
    'database_name' =>  'test',
    'server'        =>  'localhost',
    'username'      =>  'root',
    'password'      =>  'root',
    'charset'       =>  'utf8'
);

/* 自定义类库配置 */
$CONFIG['system']['lib'] = array(
    'prefix' => 'my' // 自定义类库的文件前缀
);

$CONFIG['system']['route'] = array(
    'default_controller' => 'index', // 系统默认控制器
    'default_action' => 'index', // 系统默认控制器方法
    /**
     * 定义URL的形式
     * 1 为普通模式 index.php?c=controller&a=action&id=2
     * 2 为PATHINFO index.php/controller/action/id/2(暂未实现)
     */
    'url_type' => 1
);

/* 缓存配置 */
$CONFIG['system']['cache'] = array(
    'cache_dir' => 'Runtime/Cache', // 缓存路径，相对于根目录
    'cache_prefix' => 'cache_', // 缓存文件名前缀
    'cache_time' => 1800, // 缓存时间默认1800秒
    'cache_mode' => 2 // mode 1 为json_encode ，model 2为保存为可执行文件
);

/* 模板引擎配置 */
$CONFIG['system']['template'] = array(
    'suffix' => '.html', // 模版文件后缀
    'compileDir' => 'Runtime/Template_c', //编译后的存放目录
    'cache_html' => FALSE, // 是否编译成静态的html文件
    'suffix_cache' => '.htm', // 设置编译文件后缀
    'cache_time' => '7200', // 更新时间
    'php_turn' => TRUE, // 是否支持原生php代码
    'debug' => FALSE // 调试模式
);






