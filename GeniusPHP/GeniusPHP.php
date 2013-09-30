<?php
// 定义系统路径
define('SYSTEM_PATH', str_replace('\\', '/', dirname(__FILE__)));
// 定义项目根路径
define('ROOT_PATH', substr(SYSTEM_PATH, 0, - 10));
// 定义系统核心路径
define('SYS_CORE_PATH', SYSTEM_PATH . '/Core');
// 定义系统库路径
define('SYS_LIB_PATH', SYSTEM_PATH . '/Lib');
// 定义项目库路径
define('APP_LIB_PATH', ROOT_PATH . APP_PATH . '/Lib');
// 定义项目控制器路径
define('CONTROLLER_PATH', ROOT_PATH . APP_PATH . '/Controller');
// 定义项目模型路径
define('MODEL_PATH', ROOT_PATH . APP_PATH . '/Model');
// 定义项目视图路径
define('VIEW_PATH', ROOT_PATH . APP_PATH . '/View');
// 定义项目日志路径
define('LOG_PATH', ROOT_PATH . APP_PATH . '/Log');

// 系统类
final class Genius
{

    public static $_lib = null;

    public static $_config = null;

    /**
     * 初始化
     *
     * @access public
     */
    public static function init()
    {
        // 配置自动加载的类库
        self::setAutoLibs();
        // 引入核心控制器类
        require SYS_CORE_PATH . '/model.php';
        // 引入核心控制器
        require SYS_CORE_PATH . '/controller.php';
    }

    /**
     * 创建应用
     *
     * @access public
     * @param array $config            
     */
    public static function run($config)
    {
        // 加载配置项
        self::$_config = $config['system'];
        // 初始化
        self::init();
        // 加载类库
        self::autoload();
        // 设置URL的类型
        self::$_lib['route']->setUrlType(self::$_config['route']['url_type']);
        // 将URL转发成数组
        $urlArray = self::$_lib['route']->getUrlArray();
        self::parseRoute($urlArray);
    }

    /**
     * 自动加载类库
     *
     * @access public
     * @param array $_lib            
     */
    public static function autoload()
    {
        foreach (self::$_lib as $key => $value) {
            // 包含类库
            require ($value);
            // 转换类库名首字母为大写
            $lib = ucfirst($key);
            // 实例化类库，并放入$_lib数组
            self::$_lib[$key] = new $lib();
        }
        // 初始化cache
        if (is_object(self::$_lib['cache'])) {
            self::$_lib['cache'];
        }
    }

    /**
     * 加载类库
     *
     * @access public
     * @param string $className类库名称            
     * @return object
     */
    public static function newLib($className)
    {
        $app_lib = $sys_lib = null;
        $app_lib = APP_LIB_PATH . '/' . self::$_config['lib']['prefix'] . '_' . $className . '.php';
        $sys_lib = SYS_LIB_PATH . '/lib_' . $className . '.php';
        
        if (file_exists($app_lib)) {
            require ($app_lib);
            $className = ucfirst(self::$_config['lib']['prefix']) . ucfirst($className);
            return new $className();
        } elseif (file_exists($sys_lib)) {
            require ($sys_lib);
            return self::$_lib['$className'] = new $className();
        } else {
            trigger_error('加载' . $className . '类库不存在');
        }
    }

    /**
     * 配置需要自动加载的类库
     *
     * @access public
     */
    public static function setAutoLibs()
    {
        self::$_lib = array(
            'route' => SYS_LIB_PATH . '/lib_route.php',
            'mysql' => SYS_LIB_PATH . '/lib_mysql.php',
            'template' => SYS_LIB_PATH . '/lib_template.php',
            'cache' => SYS_LIB_PATH . '/lib_cache.php',
        );
    }

    public static function parseRoute($urlArray = array())
    {
        $app = '';
        $controller = '';
        $action = '';
        $model = '';
        $params = '';
        
        // 处理分组app
        if (isset($urlArray['app'])) {
            $app = $urlArray['app'];
        }
        
        // 处理控制器和模型文件
        if (isset($urlArray['controller'])) {
            // 控制器和模型同名
            $controller = $model = $urlArray['controller'];
            if ($app) {
                $controllerFile = CONTROLLER_PATH . '/' . $app . '/' . $controller . 'Controller.php';
                $modelFile = MODEL_PATH . '/' . $app . '/' . $model . 'Model.php';
            } else {
                $controllerFile = CONTROLLER_PATH . '/' . $controller . 'Controller.php';
                $modelFile = MODEL_PATH . '/' . $model . 'Model.php';
            }
        } else {
            $controller = $model = self::$_config['route']['default_controller'];
            if ($app) {
                $controllerFile = CONTROLLER_PATH . '/' . $app . '/' . $controller . 'Controller.php';
                $modelFile = MODEL_PATH . '/' . $app . '/' . $model . 'Model.php';
            } else {
                $controllerFile = CONTROLLER_PATH . '/' . $controller . 'Controller.php';
                $modelFile = MODEL_PATH . '/' . $model . 'Model.php';
            }
        }
        
        // 处理方法
        if (isset($urlArray['action'])) {
            $action = $urlArray['action'];
        } else {
            $action = self::$_config['route']['default_action'];
        }
        
        // 处理参数
        if (isset($urlArray['params'])) {
            $params = $urlArray['params'];
        }
        
        // 实例化控制器并传入参数
        if (file_exists($controllerFile)) {
            if (file_exists($modelFile)) {
                require $modelFile;
            }
            require $controllerFile;
            $controller = $controller . 'Controller';
            $controller = new $controller();
            if ($action) {
                if (method_exists($controller, $action)) {
                    isset($params) ? $controller->$action($params) : $controller->$action;
                } else {
                    die($action . '方法不存在:' . $controllerFile);
                }
            } else {
                die('控制器方法不存在:' . $controllerFile);
            }
        } else {
            die($controller . '控制器不存在:' . $controllerFile);
        }
    }
}