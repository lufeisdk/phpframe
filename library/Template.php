<?php

namespace phpframe;

use phpframe\exception\NotFoundException;

class Template extends Component
{
    private static $_smarty;

    private $_assign = [];

    private $_config = [
        # 默认模板引擎类型，可选smarty，twig
        'type' => 'twig',
        # 模板后缀
        'view_suffix' => 'html',
        # 模板引擎普通标签开始标记
        'tpl_begin' => '{',
        # 模板引擎普通标签结束标记
        'tpl_end' => '}',
    ];

    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $config = Config::all('common', 'template');
            $instance = new self($config);
        }
        return $instance;
    }

    private function __construct($config = [])
    {
        self::$_options = array_merge($this->_config, $config);
    }

    /**
     * 加载Smarty
     * @return \Smarty
     */
    private function loadSmarty()
    {
        if (!self::$_smarty) {
            include LIB_PATH . '/driver/template/Smarty/Smarty.class.php';
            self::$_smarty = new \Smarty;
        }
        return self::$_smarty;
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param  mixed $name 要显示的模板变量
     * @param  mixed $value 变量的值
     * @return $this
     */
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->_assign = array_merge($this->_assign, $name);
        } else {
            $this->_assign[$name] = $value;
        }
    }

    /**
     * 显示视图文件,传入的文件名不带文件后缀
     * @param string $filename 视图文件名
     * type: admin@Index/index  模块@控制器/方法名
     *       Index/index 或者 Index.index 控制器/方法名
     *       index  只有视图文件名，无文件后缀
     *       传空值，默认访问的模块@控制器/方法名
     */
    public function display($filename = '')
    {
        $module = BIND_MODULE;
        $controller = BIND_CONTROLLER;
        $action = BIND_ACTION;
        $view_suffix = $this->view_suffix;
        $view = $action;
        if ($filename) {
            # admin@Index/index
            if (false !== strpos($filename, '@')) {
                $module = strstr($filename, '@', true);
                $filename = ltrim(strstr($filename, '@'), '@');
            }

            # Index/index 或者 Index.index
            if (preg_match('/[\/\.]/', $filename, $matches)) {
                list($controller, $action) = explode($matches[0], $filename);
                $view = $action;
            } else {    # 只有视图文件名的情况
                $view = $filename;
            }
        }
        $view .= '.' . $view_suffix;

        $dir = APP_PATH . $module . '/views/' . $controller;
        $file = $dir . DS . $view;
        if (!is_file($file)) {
            throw new NotFoundException('没有找到相应的视图文件：' . $file);
        }

        $cache_dir = RUNTIME_PATH . DS . 'temp' . DS . BIND_MODULE;
        if ($this->type == 'twig') {
            $loader = new \Twig\Loader\FilesystemLoader($dir);
            $twig = new \Twig\Environment($loader, [
                'cache' => $cache_dir,
                'debug' => true
            ]);

            # 自定义定界符
            $lexer = new \Twig\Lexer($twig, array(
                'tag_block' => array('{', '}'),
                'tag_variable' => array('{$', '}'),
            ));
            $twig->setLexer($lexer);

            $template = $twig->load($view);
            $template->display($this->_assign);
        } elseif ($this->type == 'smarty') {
            $this->loadSmarty();
            // self::$_smarty->caching = true;
            self::$_smarty->setTemplateDir($dir)
                ->setCompileDir($cache_dir)
                //->setCacheDir(RUNTIME_PATH . DS . 'cache' . DS . BIND_MODULE)
                ->assign($this->_assign)
                ->display($view);
        }
    }
}