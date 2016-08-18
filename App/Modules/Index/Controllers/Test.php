<?php
 
class TestController extends Controller
{
    function index()
    {
        $this->assign('title', '这是首页');
        $this->assign('content', '欢迎开发FastPHP!');
        $this->render();
    }
	public function test(){
		var_dump($_GET);
		
		$this->assign('time',date("Y-m-d H:i:s",time()));
		$this->render('index','index');
	}
}