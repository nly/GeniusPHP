<?php

/**
 * 核心控制器
 * @author niulingyun
 *
 */
class Controller
{
    public static $template;

    public function __construct()
    {}

    /**
     * 实例化模型
     *
     * @access final protected
     * @param string $model模型名称            
     * @return object
     */
    final protected function model($model)
    {
        if (empty($model)) {
            trigger_error('不能实例化空模型');
        }
        $modelName = $model . 'Model';
        return new $modelName();
    }

    /**
     * 加载类库
     *
     * @param string $lib类库名称            
     * @param bool $auto如果TRUE则默认加载系统自动加载的类库，如果为FALSE则加载自定义类库            
     * @return Ambigous <object, multitype:string >
     */
    final protected function load($lib, $auto = TRUE)
    {
        if (empty($lib)) {
            trigger_error('加载类库名不能为空');
        } elseif ($auto === TRUE) {
            return Genius::$_lib[$lib];
        } elseif ($auto === FALSE) {
            return Genius::newLib($lib);
        }
    }

    /**
     * 加载系统配置，默认为系统配置$CONFIG['system'][$config]
     *
     * @access final protected
     * @param string $config配置名            
     * @return array
     */
    final protected function config($config)
    {
        return Genius::$_config[$config];
    }
    
    /**
     * 模板文件复制
     * @param string $key
     * @param multiple $value
     */
    final protected function assign($key, $value)
    {
        Genius::$_lib['template']->assign($key,$value);
    }
    
    /**
     * 加载模板文件
     * 
     * @access final protected
     * @param string $file模板文件                    
     */
    final protected function display($file)
    {
        if (empty($file)) {
            trigger_error('不能加载空的模板文件');
        }
        Genius::$_lib['template']->display($file);      
    }
    
}