<?php
# 公共配置文件
return [
    # 应用配置
    'app' => [
        # 调试模式
        'app_debug' => true,
        # 记录追踪日志
        'log_trace' => false,
        # 默认模块名
        'default_module' => 'admin',
        # 默认控制器名
        'default_controller' => 'Index',
        # 默认方法名
        'default_action' => 'index',
        # 创建默认的控制列表
        'build_controller_list' => '',
        # 创建默认的模型列表
        'build_model_list' => '',


        # API接口Token
        'api_token' => '',

        // +----------------------------------------------------------------------
        // | URL请求设置
        // +----------------------------------------------------------------------

        // 默认全局过滤方法 用逗号分隔多个
        'default_filter' => 'strip_tags',
        // PATHINFO变量名 用于兼容模式
        'var_pathinfo' => 's',
        // 兼容PATH_INFO获取
        'pathinfo_fetch' => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
        // HTTPS代理标识
        'https_agent_name' => '',
        // IP代理获取标识
        'http_agent_ip' => 'HTTP_X_REAL_IP',
        // URL伪静态后缀
        'url_html_suffix' => 'html',
        // 域名根，如thinkphp.cn
        'url_domain_root' => '',
        // 表单请求类型伪装变量
        'var_method' => '_method',
        // 表单ajax伪装变量
        'var_ajax' => '_ajax',
        // 表单pjax伪装变量
        'var_pjax' => '_pjax',
        // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
        'request_cache' => false,
        // 请求缓存有效期
        'request_cache_expire' => null,
        // 全局请求缓存排除规则
        'request_cache_except' => [],
    ],

    # 数据库配置
    'database' => [

    ],

    # 日志配置
    'log' => [
        'file' => [
            'log_path' => '../runtime/log',   # 日志根目录
            'log_file' => 'default.log',    # 默认日志文件名
            'format' => 'Y/m/d',            # 日志自定义目录，使用日期时间定义
        ]
    ],

    # 缓存配置
    'cache' => [
        'file' => [
            'path' => '../runtime/cache',   # 缓存存储路径
            'prefix' => '',         # 缓存名称前缀
            'expire' => 86400,      # 默认缓存时间
        ],

        'redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => '',           # Redis密码
            'select' => 0,          # 分库序号
            'timeout' => 0,         # 超时连接
            'expire' => 0,          # 默认过期时间
            'persistent' => false,  # 是否长连接
            'prefix' => '',         # 缓存名称前缀
            'serialize' => 1,       # 是否序列化存储
        ],
    ],

    # 会话设置
    'session' => [
        'id' => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix' => 'think',
        // 驱动方式 支持redis memcache memcached
        'type' => '',
        // 是否自动开启 SESSION
        'auto_start' => true,
        'httponly' => true,
        'secure' => false,
    ],

    # Cookie设置
    'cookie' => [
        // cookie 名称前缀
        'prefix' => '',
        // cookie 保存时间
        'expire' => 0,
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template' => [
        // 模板引擎类型 支持 twig smarty 支持扩展
        'type' => 'smarty',
        // 模板后缀
        'view_suffix' => 'html',
        // 模板引擎普通标签开始标记
        'tpl_begin' => '{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}',
    ],

    # 中间件
    'middleware' => [],
];