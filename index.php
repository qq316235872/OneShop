<?php

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

//PHP报错设置
error_reporting(E_ALL^E_NOTICE^E_WARNING);

//开发模式环境变量前缀
define('ENV_PRE', 'OS_');

//产品名称
define('PRODUCT_NAME', 'OneShop');

//部署阶段注释或者设为false
define('APP_DEBUG', $_SERVER[ENV_PRE.'APP_DEBUG']? : true);

// 定义应用目录
define('APP_PATH','./Application/');

//缓存目录设置  此目录必须可写，建议移动到非WEB目录
define('RUNTIME_PATH', './Runtime/');

// 系统安装及开发模式检测
if(is_file(APP_PATH . 'Common/Conf/install.lock') === false && $_SERVER[ENV_PRE.'DEV_MODE'] !== 'true'){
    define('BIND_MODULE','Install');
}
 
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';