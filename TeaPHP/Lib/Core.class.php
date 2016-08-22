<?php
/**
 * TeaPHP核心框架
 */
class Core
{
    // 运行程序
    public function run()
    {
		spl_autoload_register(array($this, 'loadClass'));
        $this->setReporting();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
		//路由
		$Router = new Router();
        $Router->run();
    }
    // 检测开发环境
    public function setReporting()
    {
        if (APP_DEBUG === true) {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', RUNTIME_PATH. 'logs/error.log');
        }
    }

    // 删除敏感字符
    public function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    // 检测敏感字符并删除
    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET ) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST ) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    // 检测自定义全局变量（register globals）并移除
    public function unregisterGlobals()
    {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
           foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }
	
	/**
     * 自动加载控制器和模型类
     * @用法:(1)加载Controllers中的类 new classController (2)加载Models中的类 new classModel
	 * @用法:(3)加载Lib中的类 new Lib_class (4)加载TeaPHP中的核心类 new class
     * @param string $class 对象类名
     * @return void
     */
    public static function loadClass($class)
    {
		if(substr($class,-10) == 'Controller') {
            $className = substr($class,0,-10);
			$coreFile = CORE_PATH . 'Controller' . '.class.php';
			$file = APP_PATH . 'Controllers/' . $className . '.class.php';
        }elseif(substr($class,-5) == 'Model'){
            $className = substr($class,0,-5);
			$coreFile = CORE_PATH . 'Model' . '.class.php';
			$file = APP_PATH . 'Models/' . $className . '.class.php';
        }elseif(substr($class,0,3) == 'Lib'){
            $className = substr($class,4);
			$coreFile = CORE_PATH . 'Controller' . '.class.php';
			$file = APP_PATH . 'Lib/' . $className . '.class.php';
        }else{
			$coreFile = CORE_PATH . 'Controller' . '.class.php';
			$file = CORE_PATH . $class . '.class.php';
        }
		if(require_array(array($file,$coreFile),true)){
            return ;
        }
    }
}
