# util-page
分页类库


##使用
##### 控制器头部引入
~~~
use util\page\Page;
~~~

##### 控制器方法中使用
~~~
$config = [
    'total_num' => 100,             # 总记录数
    'page_num' => 5,                # 需要显示的页码数
    'current_page' => $_GET['page'] ?? 1,        # 当前第几页
    'show_page' => 20,  #每页显示记录数
];
$page = Page::getInstance($config);
//pp($page->getPages());
echo $page->showPages(1);
~~~