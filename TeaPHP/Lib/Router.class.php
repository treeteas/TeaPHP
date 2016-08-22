<?php
/**
 * 框架路由类
 *@param url_mode：url模式，0 动态传参模式 例如：www.ruanpower.com/index.php?m=home&c=index&a=test&aid=5 1 pathinfo模式  *例如:www.ruanpower.com/home/index/test/aid/5
 *@param var_module 模块变量名
 *@param var_controlle 控制器变量名
 *@param var_action 方法变量名
 *@author teatrees
 */
class Router {

    static private $url_mode = URL_MODE;
    static private $var_controller = VAR_CONTROLLER;
    static private $var_action = VAR_ACTION;
    static private $var_module = VAR_MODULE;

	static public function run(){
		$data = self::parseUrl();
		//实例化控制器
		$module = ucfirst(strtolower($data['module']));
        $controller = ucfirst(strtolower($data['controller']));
        $controllerClass = $controller . 'Controller';
        $action = $data['action'];
		$param[] = $data['param'];
		require  APP_PATH . 'Modules/' . $module . '/Controllers/' . $controller . '.class.php';
        $dispatch = new $controllerClass($module, $controller, $action);
        // 如果控制器和动作存在，调用并传入URL参数
        if ((int)method_exists($controllerClass, $action)) {
            call_user_func_array(array($dispatch, $action), $param);
        } else {
            exit($controller . "控制器不存在");
        }
	}
    /**
     * 获取url打包参数
     * @return type
     */
    static public function parseUrl() {
        switch (self::$url_mode) {
            //动态url传参 模式
            case 0:
                return self::getParamByDynamic();
                break;
            //pathinfo 模式
            case 1:
                return self::getParamByPathinfo();
                break;
        }
    }

    /**
     * 获取参数通过url传参模式
     */
    static private function getParamByDynamic() {
        $arr = empty($_SERVER['QUERY_STRING']) ? array() : explode('&', $_SERVER['QUERY_STRING']);
        $data = array(
            'module' => '',
            'controller' => '',
            'action' => '',
            'param' => array()
        );
        if (!empty($arr)) {
            $tmp = array();
            $part = array();
            foreach ($arr as $v) {
                $tmp = explode('=', $v);
                $tmp[1] = isset($tmp[1]) ? trim($tmp[1]) : '';
                $part[$tmp[0]] = $tmp[1];
            }
            if (isset($part[self::$var_module])) {
                $data['module'] = $part[self::$var_module];
                unset($part[self::$var_module]);
            }
            if (isset($part[self::$var_controller])) {
                $data['controller'] = $part[self::$var_controller];
                unset($part[self::$var_controller]);
            }
            if (isset($part[self::$var_action])) {
                $data['action'] = $part[self::$var_action];
                unset($part[self::$var_action]);
            }
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    unset($_GET[self::$var_controller], $_GET[self::$var_action], $_GET[self::$var_module]);
                    $data['param'] = array_merge($part, $_GET);
                    unset($_GET);
                    break;
                case 'POST':
                    unset($_POST[self::$var_controller], $_POST[self::$var_action], $_GET[self::$var_module]);
                    $data['param'] = array_merge($part, $_POST);
                    unset($_POST);
                    break;
                case 'HEAD':
                    break;
                case 'PUT':
                    break;
            }
        }
        return $data;
    }

    /**
     * 获取参数通过pathinfo模式
     */
    static private function getParamByPathinfo() {
        $part = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $data = array(
            'module' => '',
            'controller' => '',
            'action' => '',
            'param' => array()
        );
        if (!empty($part)) {
            krsort($part);
            $data['module'] = array_pop($part);
            $data['controller'] = array_pop($part);
            $data['action'] = array_pop($part);
            ksort($part);
            $part = array_values($part);
            $tmp = array();
            if (count($part) > 0) {
                foreach ($part as $k => $v) {
                    if ($k % 2 == 0) {
                        $tmp[$v] = isset($part[$k + 1]) ? $part[$k + 1] : '';
                    }
                }
            }
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    unset($_GET[self::$var_controller], $_GET[self::$var_action]);
                    $data['param'] = array_merge($tmp, $_GET);
					$_GET = array_merge($_GET,$data);
                    break;
                case 'POST':
                    unset($_POST[self::$var_controller], $_POST[self::$var_action]);
                    $data['param'] = array_merge($tmp, $_POST);
					$_POST = array_merge($_POST,$data);
                    break;
                case 'HEAD':
                    break;
                case 'PUT':
                    break;
            }
        }
        return $data;
    }
}
