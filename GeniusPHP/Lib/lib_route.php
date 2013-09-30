<?php

/**
 * URL处理类
 * @author niulingyun
 *
 */
final class Route
{

    public $url_query;

    public $url_type;

    public $route_url = array();

    public function __construct()
    {
        $this->url_query = parse_url($_SERVER['REQUEST_URI']);
    }

    /**
     * 设置URL类型
     *
     * @access public
     * @param number $url_type            
     */
    public function setUrlType($url_type = 2)
    {
        if ($url_type > 0 && $url_type < 3) {
            $this->url_type = $url_type;
        } else {
            trigger_error('指定的URL模式不存在');
        }
    }

    /**
     * 获取数组形式的URL
     *
     * @access public
     * @return multitype:
     */
    public function getUrlArray()
    {
        $this->makeUrl();
        return $this->route_url;
    }

    /**
     * 生成数组形式的URL
     *
     * @access public
     */
    public function makeUrl()
    {
        switch ($this->url_type) {
            case 1:
                $this->queryToArray();
                break;
            case 2:
                $this->pathinfoToArray();
                break;
        }
    }

    /**
     * 将query形式的URL转化成数组
     *
     * @access public
     */
    public function queryToArray()
    {
        $arr = ! empty($this->url_query['query']) ? explode('&', $this->url_query['query']) : array();
        $array = $tmp = array();
        if (count($arr) > 0) {
            foreach ($arr as $item) {
                $tmp = explode('=', $item);
                $array[$tmp[0]] = $tmp[1];
            }
            // 分组
            if (isset($array['app'])) {
                $this->route_url['app'] = $array['app'];
                unset($array['app']);
            }
            // 控制器
            if (isset($array['c'])) {
                $this->route_url['controller'] = $array['c'];
                unset($array['c']);
            }
            // 方法动作
            if (isset($array['a'])) {
                $this->route_url['action'] = $array['a'];
                unset($array['a']);
            }
            // 其他参数
            if (count($array) > 0) {
                $this->route_url['params'] = $array;
            }
        } else {
            $this->route_url = $array;
        }
    }

    /**
     * 将PATH_INFO的URL形式转化为数组
     *
     * @access public
     */
    public function pathinfoToArray()
    {}
}
