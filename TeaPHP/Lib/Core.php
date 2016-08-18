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
		$Router = new Lib_Router();
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
	
	public static function getClass($class){
		$result = array();
		if(substr($class,-10) == 'Controller') {
            $result['type'] = 1;
			$result['className'] = substr($class,0,-12);
        }elseif(substr($class,-5) == 'Model'){ // 加载模型
            $result['type'] = 2;
			$result['className'] = substr($class,0,-7);
        }elseif(substr($class,0,2) == 'Lib'){ // 加载模型
            $result['type'] = 3;
			$result['className'] = substr($class,4,-1);
        }else{
			$result['type'] = 0;
			$result['className'] = $class;
		}
		return $result; 
	}
	
    // 自动加载控制器和模型类 
    public static function loadClass($class)
    {
        //$frameworks = FRAME_PATH . $class . '.class.php';
       // $controllers = APP_PATH . 'App/Modules/Controllers/' . $class . '.class.php';
       // $models = APP_PATH . 'App/Modules/Models/' . $class . '.class.php';
		$classInfo = $this->getClass($class);
		var_dump($classInfo);exit;
		$coreFile = CORE_PATH;
		$libFile    =   LIB_PATH . substr($class,4) . '.php';
		
		$file       =   substr($class,4);
		$controllerFile    =   CONTROLLER_PATH . $class . '.php';
		$modelFile    =   MODEL_PATH . $class . '.php';
        
		
		echo $class;
		echo "<br/>";
		echo $libFile;
		echo "<br/>";
		echo $modelFile;
		echo "<br/>";
		echo $file;
		echo "<br/>";
		echo "<br/>";
		exit;
        if(substr($class,-10)=='Controller') { // 加载控制器
            if(require_array(array($libFile,$controllerFile),true)){
                return ;
            }
        }elseif(substr($class,-5)=='Model'){ // 加载模型
            if(require_array(array($libFile,$modelFile),true)){
                return ;
            }
        }
    }
	/**
     * 系统自动加载ThinkPHP类库
     * 并且支持配置自动加载路径
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        // 检查是否存在别名定义
        if(alias_import($class)) return ;
        $libPath    =   defined('BASE_LIB_PATH')?BASE_LIB_PATH:LIB_PATH;
        $group      =   defined('GROUP_NAME') && C('APP_GROUP_MODE')==0 ?GROUP_NAME.'/':'';
        $file       =   $class.'.class.php';
        if(substr($class,-8)=='Behavior') { // 加载行为
            if(require_array(array(
                CORE_PATH.'Behavior/'.$file,
                EXTEND_PATH.'Behavior/'.$file,
                LIB_PATH.'Behavior/'.$file,
                $libPath.'Behavior/'.$file),true)
                || (defined('MODE_NAME') && require_cache(MODE_PATH.ucwords(MODE_NAME).'/Behavior/'.$file))) {
                return ;
            }
        }elseif(substr($class,-5)=='Model'){ // 加载模型
            if(require_array(array(
                LIB_PATH.'Model/'.$group.$file,
                $libPath.'Model/'.$file,
                EXTEND_PATH.'Model/'.$file),true)) {
                return ;
            }
        }elseif(substr($class,-6)=='Action'){ // 加载控制器
            if(require_array(array(
                LIB_PATH.'Action/'.$group.$file,
                $libPath.'Action/'.$file,
                EXTEND_PATH.'Action/'.$file),true)) {
                return ;
            }
        }elseif(substr($class,0,5)=='Cache'){ // 加载缓存驱动
            if(require_array(array(
                EXTEND_PATH.'Driver/Cache/'.$file,
                CORE_PATH.'Driver/Cache/'.$file),true)){
                return ;
            }
        }elseif(substr($class,0,2)=='Db'){ // 加载数据库驱动
            if(require_array(array(
                EXTEND_PATH.'Driver/Db/'.$file,
                CORE_PATH.'Driver/Db/'.$file),true)){
                return ;
            }
        }elseif(substr($class,0,8)=='Template'){ // 加载模板引擎驱动
            if(require_array(array(
                EXTEND_PATH.'Driver/Template/'.$file,
                CORE_PATH.'Driver/Template/'.$file),true)){
                return ;
            }
        }elseif(substr($class,0,6)=='TagLib'){ // 加载标签库驱动
            if(require_array(array(
                EXTEND_PATH.'Driver/TagLib/'.$file,
                CORE_PATH.'Driver/TagLib/'.$file),true)) {
                return ;
            }
        }

        // 根据自动加载路径设置进行尝试搜索
        $paths  =   explode(',',C('APP_AUTOLOAD_PATH'));
        foreach ($paths as $path){
            if(import($path.'.'.$class))
                // 如果加载类成功则返回
                return ;
        }
    }
}
