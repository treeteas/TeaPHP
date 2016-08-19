<?php
// 系统信息
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
// 初始化常量
defined('FRAME_PATH') or define('FRAME_PATH', __DIR__.'/');
defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/Lib/');
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('APP_DEBUG') or define('APP_DEBUG', false);
defined('APP_FILE_CASE') or define('APP_FILE_CASE', false);// 是否检查文件的大小写 对Windows平台有效
defined('LIB_PATH') or define('LIB_PATH', APP_PATH.'Lib/'); // 项目类库目录
defined('CONTROLLER_PATH') or define('CONTROLLER_PATH', APP_PATH.'Controllers/'); // 项目模型类库目录
defined('MODEL_PATH') or define('MODEL_PATH', APP_PATH.'Models/'); // 项目模型类库目录
defined('CONFIG_PATH') or define('CONFIG_PATH', APP_PATH.'conf/');
defined('RUNTIME_PATH') or define('RUNTIME_PATH', APP_PATH.'runtime/');
defined('VAR_MODULE') or define('VAR_MODULE', 'm');
defined('VAR_CONTROLLER') or define('VAR_CONTROLLER', 'c');
defined('VAR_ACTION') or define('VAR_ACTION', 'a');
defined('VIEW_EXT') or define('VIEW_EXT', 'html');//模板文件类型
// 包含配置文件
require './Conf/config.php';

// 加载系统基础函数库
require FRAME_PATH . 'Common/common.php';

//包含核心框架类
require CORE_PATH . 'Core.class.php';

// 实例化核心类
$TeaPHP = new Core;
$TeaPHP->run();