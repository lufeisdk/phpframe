<?php

namespace util\page;

class Page
{
    private $_config = [
        'total_num' => 0,           # 总记录数
        'page_num' => 5,            # 需要显示的页码数
        'total_page' => 0,          # 总页数
        'current_page' => 1,        # 当前第几页
        'show_page' => 20,          # 每页显示记录数，默认20条
        'href' => '',               # 分页链接
    ];

    protected $page_arr = array();  # 保存生成的页码 键页码 值为连接

    /**
     * 使用 $this->name 获取配置
     * @access public
     * @param  string $name 配置名称
     * @return mixed    配置值
     */
    public function __get($name)
    {
        return $this->_config[$name];
    }

    /**
     * 设置验证码配置
     * @access public
     * @param  string $name 配置名称
     * @param  string $value 配置值
     * @return void
     */
    public function __set($name, $value)
    {
        if (isset($this->_config[$name])) {
            $this->_config[$name] = $value;
        }
    }

    /**
     * 检查配置
     * @access public
     * @param  string $name 配置名称
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_config[$name]);
    }

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
        $this->_config = array_merge($this->_config, $config);

        if (empty($this->href)) {
            $this->href = htmlentities($_SERVER['PHP_SELF']);
        }

        $this->construct_Pages();
    }

    /**
     * getPages 返回页码数组
     * @return array 一维数组 键为页码 值为链接
     */
    public function getPages()
    {
        return $this->page_arr;
    }

    /**
     * showPages 返回生成好的页码
     * @param int $style 样式
     * @return string 生成好的页码
     */
    public function showPages($style = 1)
    {
        $func = 'pageStyle' . $style;
        return $this->$func();
    }

    /**
     * pageStyle1 分页样式（可参照这个添加自定义样式 例如pageStyle2（））
     * 样式 共45条记录,每页显示10条,当前第1/4页 [首页] [上页] [1] [2] [3] .. [下页] [尾页]
     * @return string
     */
    protected function pageStyle1()
    {
        /**
         * 构造普通模式的分页
         * 共4523条记录,每页显示10条,当前第1/453页 [首页] [上页] [1] [2] [3] .. [下页] [尾页]
         */
        $pageStr = '<div class="page">';
        $pageStr .= '共' . $this->total_num . '条记录，每页显示' . $this->show_page . '条，';
        $pageStr .= '当前第' . $this->current_page . '/' . $this->total_page . '页 ';

        $_GET['page'] = 1;
        $pageStr .= '<span>[<a href="' . $this->href . '?' . http_build_query($_GET) . '">首页</a>] </span>';
        //如果当前页不是第一页就显示上页
        if ($this->current_page > 1) {
            $_GET['page'] = $this->current_page - 1;
            $pageStr .= '<span>[<a href="' . $this->href . '?' . http_build_query($_GET) . '">上页</a>] </span>';
        }

        foreach ($this->page_arr as $k => $v) {
            $active = $this->current_page == $k ? 'class="active"' : '';
            $pageStr .= '<span ' . $active . '>[<a href="' . $v . '">' . $k . '</a>] </span>';
        }

        //如果当前页小于总页数就显示下一页
        if ($this->current_page < $this->total_page) {
            $_GET['page'] = $this->current_page + 1;
            $pageStr .= '<span>[<a href="' . $this->href . '?' . http_build_query($_GET) . '">下页</a>] </span>';
        }

        $_GET['page'] = $this->total_page;
        $pageStr .= '<span>[<a href="' . $this->href . '?' . http_build_query($_GET) . '">尾页</a>] </span>';
        $pageStr .= '</div>';

        return $pageStr;
    }

    /**
     * construct_Pages 生成页码数组
     * 键为页码，值为链接
     * $this->page_arr=Array(
     * [1] => index.php?page=1
     * [2] => index.php?page=2
     * [3] => index.php?page=3
     * ......)
     */
    protected function construct_Pages()
    {
        //计算总页数
        $this->total_page = ceil($this->total_num / $this->show_page);

        //根据当前页计算前后页数
        $leftPage_num = floor($this->page_num / 2);
        $rightPage_num = $this->page_num - $leftPage_num;

        //左边显示数为当前页减左边该显示的数 例如总显示7页 当前页是5 左边最小为5-3 右边为5+3
        $left = $this->current_page - $leftPage_num;
        $left = max($left, 1); //左边最小不能小于1
        $right = $left + $this->page_num - 1; //左边加显示页数减1就是右边显示数
        $right = min($right, $this->total_page); //右边最大不能大于总页数
        $left = max($right - $this->page_num + 1, 1); //确定右边再计算左边，必须二次计算

        for ($i = $left; $i <= $right; $i++) {
            $_GET['page'] = $i;
            $this->page_arr[$i] = $this->href . '?' . http_build_query($_GET);
        }
    }
}