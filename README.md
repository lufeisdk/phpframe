# PHPFrame
PHP框架

## 介绍
这是一个自定义PHP框架，包含admin管理后台（目前只有登录功能，其他有待完善），api接口项目（包括基础的用户注册，登录，修改用户信息等基础接口），command命令行执行项目（用于执行脚本）。
### 项目目录介绍：
  + app 项目目录
  + data 数据库文件目录
  + extend 扩展类目录
  + library 框架核心类目录
  + public 入口文件及静态资源目录

### 框架实现：
  - MVC架构，model层支持类ThinkPHP的链式数据查询
  - api接口实现JWT鉴权，中间件
  - 支持PDO连接数据库（MySQL，SQL Server）
  - 支持缓存（本地文件缓存，Redis）
  - 支持日志类扩展
  - 支持Smarty和Twig模板引擎
  - 命令行执行时，接收参数支持等号（=），点号（.），冒号（:）等表现形式，例如：php cli.php index index a.name age=18 gender:man
  - 支持trait

