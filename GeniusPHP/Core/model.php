<?php

/**
 * 核心控制器类
 * @author niulingyun
 */
class Model
{

    protected $db = null;

    final public function __construct()
    {
        header('Content-Type:text/html;charset=utf-8');
        // 加载数据库抽象层
        $this->db = $this->load('mysql');
    }

    /**
     * 根据表前缀获取表名
     *
     * @access final proteted
     * @param string $tableName表名            
     * @return string
     */
    final protected function table($tableName)
    {
        $config_db = $this->config('db');
        return $config_db['db_table_prefix'] . $tableName;
    }

    /**
     * 加载类库
     *
     * @param string $lib类库名称            
     * @param bool $my如果是FALSE默认加载系统自动加载的类库，如果为TRUE则加载自定义类库            
     * @return Ambigous <object, multitype:string >
     */
    final protected function load($lib, $my = FALSE)
    {
        if (empty($lib)) {
            trigger_error('加载类库名不能为空');
        } elseif ($my === FALSE) {
            return Genius::$_lib[$lib];
        } elseif ($my === TRUE) {
            return Genius::newLib($lib);
        }
    }

    /**
     * 加载系统配置，默认为系统配置 $CONFIG['system']['$config']
     *
     * @access final protected
     * @param string $config            
     * @return array
     */
    final protected function config($config = '')
    {
        return Genius::$_config[$config];
    }
}