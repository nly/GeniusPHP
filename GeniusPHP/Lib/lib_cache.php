<?php

/**
 * 缓存类
 * @author niulingyun
 *
 */
final class Cache
{
    // 配置数组
    private $arrayConfig = '';

    /**
     * 初始化参数
     */
    public function __construct()
    {
        $this->arrayConfig = Genius::$_config['cache'];
        $this->arrayConfig['cache_dir'] = ROOT_PATH . APP_PATH . '/' . $this->arrayConfig['cache_dir'];
    }

    /**
     * 设置缓存
     *
     * @param string $id            
     * @param array $data            
     */
    public function set($id, $data)
    {
        if (! isset($id)) {
            return false;
        }
        $cache = array(
            'file' => $this->getFileName($id, $this->arrayConfig['cache_dir']),
            'data' => $data
        );
        return $this->writeCache($cache);
    }

    /**
     * 获取缓存
     *
     * @param string $id            
     * @return array
     */
    public function get($id)
    {
        if (! $this->hasCache($id)) {
            return false;
        }
        return $this->getCacheData($id);
    }

    /**
     * 获取缓存目录
     *
     * @param unknown $file            
     * @return string
     */
    public function getCacheDir()
    {
        return trim($this->arrayConfig['cache_dir']);
    }

    /**
     * 获取完整的缓存文件名称
     *
     * @param unknown $id            
     * @return string
     */
    public function getFileName($id)
    {
        return $this->getCacheDir() . '/' . $this->arrayConfig['cache_prefix'] . $id . '.php';
    }

    /**
     * 根据缓存文件返回缓存名称
     *
     * @param type $file            
     * @return boolean string
     */
    public function getCacheName($file)
    {
        if (! file_exists($file)) {
            return false;
        }
        $filename = basename($file);
        preg_match('/^' . $this->arrayConfig['cache_prefix'] . '(.*).php/i', $filename, $matches);
        return $matches[1];
    }

    /**
     * 写入缓存
     *
     * @param array $cache            
     * @return boolean
     */
    public function writeCache($cache = array())
    {
        // 创建目录或者更改目录权限
        $cacheDir = $this->getCacheDir();
        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0777);
        } elseif (! is_writable($cacheDir)) {
            chmod($cacheDir, 0777);
        }
        // 处理缓存数据
        if ($this->arrayConfig['cache_mode'] == 1) {
            $content = json_encode($cache['data']);
        } else {
            $content = '<?php\n' . '   return ' . var_export($cache['data'], true) . ';\n';
        }
        // 写入缓存
        $fp = @fopen($cache['file'], w);
        if ($fp == false) {
            @flock($fp, LOCK_EX);
            if (fwrite($fp, $content) === false) {
                trigger_error('写入缓存失败:' . $cache['file']);
            }
            @flock($fp, LOCK_UN);
            @fclose($fp);
            @chmod($cache['file'], 0777);
            return true;
        } else {
            trigger_error('打开文件失败:' . $cache['file']);
            return false;
        }
    }

    /**
     * 检查缓存是否存在
     *
     * @param string $id            
     * @return boolean
     */
    public function hasCache($id)
    {
        // 删除过期的缓存
        $filename = $this->getFileName($id);
        if (file_exists($filename)) {
            if (time() > filemtime($filename) + $this->arrayConfig['cache_time']) {
                unlink($filename);
            }
        }
        return file_exists($filename);
    }

    /**
     * 删除单条缓存
     *
     * @param string $id            
     * @return boolean
     */
    public function deleteCache($id)
    {
        if ($this->hasCache($id)) {
            return unlink($this->getFileName($id));
        } else {
            trigger_error('缓存文件不存在:' . $this->getFileName($id));
        }
    }

    /**
     * 获取缓存数据
     * 
     * @param string $id            
     * @return boolean mixed
     */
    public function getCacheData($id)
    {
        if (! $this->hasCache($id)) {
            return false;
        }
        $filename = $this->getFileName($id);
        if ($this->arrayConfig['cache_mode'] == 1) {
            $data = file_get_contents($filename);
            return json_decode($data, true);
        } else {
            return include $filename;
        }
    }

    /**
     * 清空缓存
     *
     * @return boolean
     */
    public function flushCache()
    {
        $glob = @glob($this->getCacheDir() . '/' . $this->arrayConfig['cache_prefix'] . '*');
        if ($glob) {
            foreach ($glob as $item) {
                $id = $this->getCacheName($item);
                $this->deleteCache($id);
            }
        }
        return true;
    }
}