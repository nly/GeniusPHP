<?php

/**
 * 模板引擎类
 * @author niulingyun
 *
 */
final class Template
{

    private $arrayConfig = '';

    public $file; // 模版文件名，不带路径
    private $value = array();

    private $compileTool; // 编译器
    public $debug = array(); // 调试信息
    private $controlData = array();

    public function __construct()
    {
        $this->arrayConfig = Genius::$_config['template'];
        $this->arrayConfig['templateDir'] = VIEW_PATH;
        $this->arrayConfig['compileDir'] = ROOT_PATH . APP_PATH . '/' . $this->arrayConfig['compileDir'];
        $this->debug['begin'] = microtime(true);
        $this->arrayConfig = $this->arrayConfig;
        if (! is_dir($this->arrayConfig['templateDir'])) {
            exit('模版目录未找到');
        }
        if (! is_dir($this->arrayConfig['compileDir'])) {
            mkdir($this->arrayConfig['compileDir'], 0770, true);
        }
    }

    /**
     * 单步设置引擎
     *
     * @param string $key            
     * @param string $value            
     */
    public function setConfig($key, $value = null)
    {
        if (is_array($key)) {
            $this->arrayConfig = $key + $this->arrayConfig;
        } else {
            $this->arrayConfig[$key] = $value;
        }
    }

    /**
     * 获取当前模版引擎配置
     *
     * @param string $key            
     * @return string
     */
    public function getConfig($key = null)
    {
        if ($key) {
            return $this->arrayConfig[$key];
        } else {
            return $this->arrayConfig;
        }
    }

    /**
     * 注入单个变量
     *
     * @param string $key            
     * @param string $value            
     */
    public function assign($key, $value)
    {
        $this->value[$key] = $value;
    }

    public function path()
    {
        return $this->arrayConfig['templateDir'] . '/' . $this->file . $this->arrayConfig['suffix'];
    }

    /**
     * 判断是否开启了缓存判断是否开启了缓存
     *
     * @return string
     */
    public function needCache()
    {
        return $this->arrayConfig['cache_html'];
    }

    /**
     * 是否需要重新生成静态文件
     *
     * @param string $file            
     * @return boolean
     */
    public function reCache($file)
    {
        $flag = false;
        $cacheFile = $this->arrayConfig['compileDir'] . '/' . md5($file) . $this->arrayConfig['suffix_cache'];
        if ($this->arrayConfig['cache_html'] === true) { // 是否需要缓存
            $timeFlag = (time() - @filemtime($cacheFile)) < $this->arrayConfig['cache_time'] ? true : false;
            if (is_file($cacheFile) && filesize($cacheFile) > 1 && $timeFlag) { // 缓存存在为过期
                $flag = true;
            } else {
                $flag = false;
            }
        }
        return $flag;
    }

    /**
     * 模版展示
     *
     * @param string $file            
     */
    public function display($file)
    {
        $this->file = $file;
        if (! is_file($this->path())) {
            exit('找不到对应的模版:' . $this->path());
        }
        $compileFile = $this->arrayConfig['compileDir'] . '/' . md5($file) . '.php';
        $cacheFile = $this->arrayConfig['compileDir'] . '/' . md5($file) . $this->arrayConfig['suffix_cache'];
        if ($this->reCache($file) === false) {
            $this->debug['cached'] = false;
            $this->compileTool = new Compile($this->path(), $compileFile, $this->arrayConfig);
            if ($this->needCache()) {
                ob_start();
            }
            extract($this->value, EXTR_OVERWRITE);
            if (! is_file($compileFile) || filemtime($compileFile) < filemtime($this->path())) {
                $this->compileTool->vars = $this->value;
                $this->compileTool->compile();
                include $compileFile;
            } else {
                include $compileFile;
            }
            if ($this->needCache()) {
                $message = ob_get_contents();
                file_put_contents($cacheFile, $message);
            }
        } else {
            readfile($cacheFile);
            $this->debug['cached'] = true;
        }
        $this->debug['spend'] = microtime(true) - $this->debug['begin'];
        $this->debug['count'] = count($this->value);
        $this->debugInfo();
    }

    /**
     * 输出调试信息
     */
    public function debugInfo()
    {
        if ($this->arrayConfig['debug'] === true) {
            echo '<br />-----------调试信息----------';
            echo '<br />程序运行时间:', date('Y-m-d H:i:s');
            echo '<br />模版解析耗时:', $this->debug['spend'], '秒';
            echo '<br />模版包含标签数:', $this->debug['count'];
            echo '<br />是否使用静态缓存:', $this->debug['cached'];
            echo '<br />模版引擎实例参数:', var_dump($this->getConfig());
        }
    }

    /**
     * 清理缓存的HTML文件
     */
    public function clean($path = null)
    {
        if ($path === null) {
            $path = $this->arrayConfig['compileDir'];
            $path = glob($path . '/*.' . $this->arrayConfig['suffix_cache']);
        } else {
            $path = $this->arrayConfig['compileDir]'] . md5($path) . $this->arrayConfig['suffix_cache'];
        }
        foreach ((array) $path as $v) {
            unlink($v);
        }
    }
}

/**
 * 模板引擎编译器
 *
 * @author niulingyun
 *        
 */
final class Compile
{

    private $template; // 待编译的文件
    private $content; // 需要替换的文本
    private $comfile; // 编译后的文件
    private $left = '{'; // 左定界符
    private $right = '}'; // 右定界符
    private $value = array(); // 值栈
    private $phpTurn; // 是否支持原生php代码
    private $T_P = array(); // 匹配规则
    private $T_R = array(); // 替换规则
    public function __construct($template, $compileFile, $config)
    {
        $this->template = $template;
        $this->comfile = $compileFile;
        $this->content = file_get_contents($this->template);
        if ($config['php_turn'] === false) {
            $this->T_P[] = "#<\?(php)(.+?)\?>#is";
            $this->T_R[] = "&lt;? '$1' '$2'? &gt;";
        }
        $this->T_P[] = "#\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}#";
        $this->T_P[] = "#\{(loop|foreach) \\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}#i";
        $this->T_P[] = "#\{\/(loop|foreach|if)\}#i";
        $this->T_P[] = "#\{([k|v])\}#i";
        $this->T_P[] = "#\{if(.*?)\}#i";
        $this->T_P[] = "#\{(else if|elseif)(.*?)\}#i";
        $this->T_P[] = "#\{else\}#i";
        $this->T_P[] = "#\{(\#|\*)(.*?)(\#|\*)\}#";
        
        $this->T_R[] = "<?php echo \$this->value['$1']; ?>";
        $this->T_R[] = "<?php foreach((array)\$this->value['$2'] as \$k=>\$v){ ?>";
        $this->T_R[] = "<?php } ?>";
        $this->T_R[] = "<?php echo \$$1; ?>";
        $this->T_R[] = "<?php if($1){ ?>";
        $this->T_R[] = "<?php }elseif('$2'){ ?>";
        $this->T_R[] = "<?php }else{ ?>";
        $this->T_R[] = "";
    }

    public function compile()
    {
        $this->c_var2();
        $this->c_staticFile();
        file_put_contents($this->comfile, $this->content);
    }

    public function c_var2()
    {
        $this->content = preg_replace($this->T_P, $this->T_R, $this->content);
    }

    /**
     * 加入对JavaScript、css文件的解析
     */
    public function c_staticFile()
    {
        $this->content = preg_replace('#\{load (.*?)\.css\}#', '<link type="text/css" rel="stylesheet" href="$1.css' . '?t=' . time() . '" />', $this->content);
        $this->content = preg_replace('#\{load (.*?)\.js\}#', '<script type="text/javascript" src="$1.js' . '?t=' . time() . '"></script>', $this->content);
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
