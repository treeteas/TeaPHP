<?php
// 初始化常量
defined('FRAME_PATH') or define('FRAME_PATH', __DIR__.'/');
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('APP_DEBUG') or define('APP_DEBUG', false);
defined('CONFIG_PATH') or define('CONFIG_PATH', APP_PATH.'conf/');
defined('RUNTIME_PATH') or define('RUNTIME_PATH', APP_PATH.'runtime/');
defined('VAR_MODULE') or define('VAR_MODULE', 'm');
defined('VAR_CONTROLLER') or define('VAR_CONTROLLER', 'c');
defined('VAR_ACTION') or define('VAR_ACTION', 'a');
defined('VIEW_EXT') or define('VIEW_EXT', 'html');//模板文件类型
// 包含配置文件
require APP_PATH . 'Conf/config.php';

//包含核心框架类
require FRAME_PATH . 'Core.php';

// 实例化核心类
$TeaPHP = new Core;
$TeaPHP->run();