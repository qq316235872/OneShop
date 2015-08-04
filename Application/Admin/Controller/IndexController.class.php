<?php
namespace Admin\Controller;

/**
 * 后台首页控制器
 */

class IndexController extends AdminController{
    
    /**
     * 后台首页
     */
    public function index(){
        $this->meta_title = '管理首页';
        $this->display('Public/index');
    }
    
    
    /**
     * 完全删除指定文件目录
     */
    public function rmdirr($dirname = RUNTIME_PATH){
        $file = new \Common\Util\File();
        $result = $file->del_dir($dirname);
        $this->os_redirect($result,'缓存清理');
    }
}