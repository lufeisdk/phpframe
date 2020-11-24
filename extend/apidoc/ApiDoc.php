<?php

namespace util\apidoc;

use phpframe\Component;

class ApiDoc extends Component
{
    private static $_config = [
        'project_name' => 'API接口文档',    # 文档名称
        'api_path' => APP_PATH . 'api/controllers',  # API接口所在目录
        'apidoc_savepath' => ROOT_PATH . '/docs', # 生成API文档存放地址
        'apidoc_name' => 'apidoc.html',              # 生成API文档文件名
        'url' => 'http://localhost',                 # 接口域名地址
        'note_regex' => '/(\/\*\*.*?\*\s(api)?.*?\*\/\s*(public|private|protected)?\s*function\s+.*?\s*?\()/s',
        'controllerChange' => false,    # 是否转换控制器名称转为下划线命名法
        'controllerTimes' => 1,         # 设置大写字母出现次数，超过则转换
        'methodChange' => false,        # 是否转换方法名为下划线命名法
        'methodTimes' => 2,             # 设置大写字母出现次数，超过则转换

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
        self::$_options = array_merge(self::$_config, $config);
    }

    /**
     * 设置文档项目名称
     * @param string $name 项目名称
     * @return void
     */
    public function setProjectName($name)
    {
        $this->project_name = $name;
        return $this;
    }

    /**
     * 设置是否开启驼峰转匈牙利
     * @param bool $controller 文件名 true/false
     * @param bool $method 方法名 true/false
     * @return void
     */
    public function setChange($controller = true, $method = true)
    {
        $this->controllerChange = $controller;
        $this->methodChange = $method;
        return $this;
    }

    /**
     * 驼峰转匈牙利转换条件 (出现几次大写字母才转换)
     * @param integer $controller 文件名
     * @param integer $method 方法名
     * @return void
     */
    public function setTimes($controller = 1, $method = 2)
    {
        $this->controllerTimes = $controller;
        $this->methodTimes = $method;
        return $this;
    }

    // 生成API文档
    public function make()
    {
        $fileList = $this->getFileList();
        if (empty($fileList)) {
            die('找不到需要解析的接口文件~');
        }

        $inputData = ''; // 主体部分表格
        $rightList = []; // 侧边栏列表
        foreach ($fileList as $file) {
            $fileData = file_get_contents($file);
            $data = $this->catchEvery($fileData);

            if (empty($data)) {
                continue;
            }

            foreach ($data as $one) {
                $infoData = $this->parse($one, $file);
                if ($infoData != false) {
                    $rightList[basename($file)][] = [
                        'methodName' => $infoData['methodName'],
                        'requestUrl' => $infoData['requestUrl'],
                    ];
                    $inputData .= $this->makeTable($infoData);
                }
            }
        }

        $tempData = file_get_contents(dirname(__FILE__) . '/temp.html');
        $tempData = str_replace('{name}', $this->project_name, $tempData);
        $tempData = str_replace('{main}', $inputData, $tempData);
        $tempData = str_replace('{right}', $this->makeRight($rightList), $tempData);
        $tempData = str_replace('{date}', date('Y-m-d H:i:s'), $tempData);
        file_put_contents($this->apidoc_savepath . DS . $this->apidoc_name, $tempData);
        return $tempData;
    }

    /**
     * 生成侧边栏
     * @param array $rightList 侧边列表数组
     * @return string html代码
     */
    private function makeRight($rightList)
    {
        $return = '';
        foreach ($rightList as $d => $file) {
            $return .= '<blockquote class="layui-elem-quote layui-quote-nm right-item-title">' . $d . '</blockquote>
            <ul class="right-item">';
            $i = 1;
            foreach ($file as $one) {
                $return .= '<li><a href="#' . base64_encode($one['requestUrl']) . '"><cite>' . $i . '、' . $one['methodName'] . '</cite><em>' . $one['requestUrl'] . '</em></a></li>';
                $i++;
            }
            $return .= '</ul>';
        }

        return $return;
    }

    /**
     * 每个API生成表格
     * @param array $data 每个API的信息 由parse返回的
     * @return string html代码
     */
    private function makeTable($data)
    {
        $return = '<div id="' . base64_encode($data['requestUrl']) . '" class="api-main">
        <div class="title">' . $data['methodName'] . '</div>
        <div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>' . $data['requestName'] . '</th>
                        <th>' . $data['requestUrl'] . '</th>
                    </tr>
                </thead>
            </table>
        </div>';
        if (count($data['param']) != 0) {
            $return .= '<div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>请求参数</th>
                        <th>参数类型</th>
                        <th>参数说明</th>
                    </tr>
                </thead>
                <tbody>';
            foreach ($data['param'] as $param) {
                $return .= '<tr>
                <td>' . $param['var'] . '</td>
                <td>' . $param['type'] . '</td>
                <td>' . $param['about'] . '</td>
            </tr>';
            }
            $return .= '</tbody>
            </table>
        </div>';
        }
        if (count($data['return']) != 0) {
            $return .= '<div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>返回名称</th>
                        <th>返回类型</th>
                        <th>返回说明</th>
                    </tr>
                </thead>
                <tbody>';
            foreach ($data['return'] as $param) {
                $return .= '<tr>
                <td>' . $param['var'] . '</td>
                <td>' . $param['type'] . '</td>
                <td>' . $param['about'] . '</td>
            </tr>';
            }
            $return .= '</tbody>
            </table>
        </div>';
        }

        $return .= ' <hr>
        </div>';

        return $return;
    }

    /**
     * 解析每一条可以生成API文档的注释成数组
     * @param string $data 注释文本 catchEvery返回的每个元素
     * @param string $fileName 文件名
     * @return array
     */
    private function parse($data, $fileName)
    {
        $return = [];
        $fileName = basename($fileName, '.php');
        preg_match_all('/(public|private|protected)?\s*function\s+(?<funcName>.*?)\(/', $data, $matches);
        $return['funcName'] = !empty($matches['funcName'][0]) ? $matches['funcName'][0] : '[null]';
        preg_match_all('/\/\*\*\s+\*\s+(?<methodName>.*?)\s+\*/s', $data, $matches);
        $return['methodName'] = !empty($matches['methodName'][0]) ? $matches['methodName'][0] : '[null]';
        preg_match_all('/\s+\*\s+\@method\s+(?<requestName>.*)?.*/', $data, $matches);
        $return['requestName'] = !empty($matches['requestName'][0]) ? $matches['requestName'][0] : '[null]';
        preg_match_all('/\s+\*\s+\@url\s+(?<requestUrl>.*)?.*/', $data, $matches);
        $return['requestUrl'] = !empty($matches['requestUrl'][0]) ? $matches['requestUrl'][0] : '[null]';
        if ($return['requestName'] == '[null]' && $return['requestUrl'] == '[null]') {
            return false;
        }
        if ($this->controllerChange == true) {
            $return['requestUrl'] = str_replace('{controller}', $this->humpToLine($fileName, $this->controllerTimes), $return['requestUrl']);
        }
        if ($this->methodChange == true) {
            $return['requestUrl'] = str_replace('{action}', $this->humpToLine($return['funcName'], $this->methodTimes), $return['requestUrl']);
        }
        $return['requestUrl'] = str_replace('{url}', $this->url, $return['requestUrl']);

        preg_match_all('/\s+\*\s+@param\s+(.*?)\s+(.*?)\s+(.*?)\s/', $data, $matches);
        if (empty($matches[1])) {
            $return['param'] = [];
        } else {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $type = !empty($matches[1][$i]) ? $matches[1][$i] : '[null]';
                $var = !empty($matches[2][$i]) ? $matches[2][$i] : '[null]';
                $about = !empty($matches[3][$i]) ? $matches[3][$i] : '[null]';
                $return['param'][] = [
                    'type' => $type,
                    'var' => $var,
                    'about' => $about,
                ];
            }
        }
        preg_match_all('/\s+\*\s+@return\s+(.*?)\s+(.*?)\s+(.*?)\s/', $data, $matches);
        $return['return'] = [];
        if (empty($matches[1])) {
            $return['return'] = [];
        } else {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $type = !empty($matches[1][$i]) ? $matches[1][$i] : '[null]';
                $var = !empty($matches[2][$i]) ? $matches[2][$i] : '[null]';
                $about = !empty($matches[3][$i]) ? $matches[3][$i] : '[null]';
                if (strpos($about, '*/') !== false) {
                    $about = $var;
                    $var = '';
                }

                if ($var != '*/' and $var != '') {
                    $return['return'][] = [
                        'type' => $type,
                        'var' => $var,
                        'about' => $about,
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * 大驼峰命名法转匈牙利命名法 IndexController => index_controller
     * @param string $str 字符串
     * @param integer $times 出现几次大写字母才转换,默认1次
     * @return string
     */
    private function humpToLine($str, $times = 1)
    {
        if (preg_match_all('/[A-Z]/', $str) >= $times) {
            $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
                return '_' . strtolower($matches[0]);
            }, $str);
            $str = ltrim($str, '_');
        }

        return $str;
    }

    /**
     * 获取代码文件中所有可以生成api的注释
     * @param string $data 代码文件内容
     */
    private function catchEvery($data)
    {
        if (preg_match_all($this->note_regex, $data, $matches)) {
            return $matches[1];
        }
        return [];
    }

    /**
     * 获取API项目下的接口文件
     * @return array
     */
    private function getFileList()
    {
        if (!is_dir($this->api_path)) {
            die('API地址解析错误');
        }

        $fileList = [];
        foreach (glob($this->api_path . '/*.php') as $file) {
            array_push($fileList, $file);
        }
        return $fileList;
    }
}