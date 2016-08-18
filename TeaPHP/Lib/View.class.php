<?php
/**
 * 视图基类
 */
class View
{
    protected $variables = array();
    protected $_module;
    protected $_controller;
    protected $_action;

    function __construct($module, $controller, $action)
    {
        $this->_module = $module;
        $this->_controller = $controller;
        $this->_action = $action;
    }
 
    // 分配变量
    public function assign($name, $value)
    {
        $this->variables[$name] = $value;
    }
 
    // 渲染显示
    public function render($templateFile = '', $controller = '')
    {
        extract($this->variables);
		$controller = empty($controller) ? '/Views/' : '/Views/'.$controller.'/';
		$templateFile = empty($templateFile) ? $this->_action : $templateFile;
        $defaultHeader = APP_PATH . 'App/Modules/' . $this->_module . '/Views/Common/header.'.VIEW_EXT;
        $defaultFooter = APP_PATH . 'App/Modules/' . $this->_module . '/Views/Common/footer.'.VIEW_EXT;
		
        $template = APP_PATH . 'App/Modules/' . $this->_module . $controller . $templateFile . '.'.VIEW_EXT;
        // 默认模板头文件
        if (file_exists($defaultHeader)) {
            include ($defaultHeader);
        }

        // 模板文件
        if (file_exists($template)) {
            include ($template);
        } else {
			exit($templateFile . "模板文件不存在");
		}
        
        // 默认模板页脚文件
        if (file_exists($defaultFooter)) {
            include ($defaultFooter);
        }
    }
}