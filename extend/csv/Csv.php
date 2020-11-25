<?php

namespace util\csv;

use phpframe\Component;
use phpframe\Request;

class Csv extends Component
{
    private $_config = [
        'file_name' => 'default.csv',
        'save_path' => ROOT_PATH . DS . 'download' . DS,
    ];

    public static function getInstance($config = [])
    {
        static $instance;
        if (!$instance) {
            $instance = new self($config);
        }
        return $instance;
    }

    private function __construct($config)
    {
        self::$_options = array_merge($this->_config, $config);
    }

    /**
     * 设置文件名
     * @param $name 设置导出CSV文件名
     * @return $this
     */
    public function setFileName($name)
    {
        $this->file_name = $name;
        return $this;
    }

    /**
     * 设置保存路径
     * @param $path 保存路径
     * @return $this
     */
    public function setSavePath($path)
    {
        $this->save_path = $path;
        return $this;
    }

    /**
     * 导出数据
     * @param array $data
     * @param bool $is_download 是否直接下载
     * @return bool
     */
    public function export($data = [], $is_download = false)
    {
        if (empty($data)) {
            return false;
        }

        $file = $this->save_path . $this->file_name;
        $fp = fopen($file, 'w');
        foreach ($data as $item) {
            fputcsv($fp, $item);
        }
        fclose($fp);
        
        if ($is_download) {
            $this->download();
        }
        return true;
    }

    /**
     * 导入CSV文件中的数据
     * @param string $file 文件地址
     * @return \Generator
     */
    public function import($file = '')
    {
        if (!$file || !is_file($file)) {
            die('文件不存在~');
        }

        $file = fopen($file, 'r');
        while ($data = fgetcsv($file)) {
            yield $data;
        }
    }

    /**
     * 下载文件
     */
    public function download()
    {
        $url = Request::getInstance()->domain(true);
        $url .= "/download/" . $this->file_name;
        exit('<script>location.href="' . $url . '"</script>');
    }
}