<?php
namespace Admin\Controller;
/**
 * 后台SEO管理控制器
 * @author 逆水行舟丶 <316235872@qq.com>
 */
class SeoController extends AdminController {
    Public $SeoRuleModel;
    Public function _initialize() {
        parent::_initialize();
        $this->SeoRuleModel=D('Common/SeoRule');
        $this->meta_title = 'Seo管理';
    }
    
    /**
     * 后台SEO列表
     */
    public function index(){
        $pid = I('get.pid', 0);
        $map = array('status' => array('EGT', 0),'pid'=>$pid);
        $title      =   trim(I('get.title'));
        if($title){$map['title'] = array('like',"%$title%");}
        $list = $this->lists($this->SeoRuleModel, $map,'sort asc');
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('pid', $pid);
        $this->assign('list', $list)->display();
 
    }
    /**
     * 编辑SEO信息
     */
    public function edit($id = 0){
        if(IS_POST){
                $data = $this->SeoRuleModel->create();
                $data['action']=I('act');
                $this->editRow('SeoRule',$data,NULL,array('url'=>U('index')));
            } else {
                $info = $this->SeoRuleModel->field(true)->find($id);
                $this->assign('info', $info)->display();
            }
    }
    
    
    /**
     * 设置状态
     */
    public function updateStatus(){
         $this->setStatus('SeoRule');
    }
    
    /**
     * 添加SEO信息
     */
    public function add(){
        if(IS_POST){
            $data = $this->SeoRuleModel->create();
            $data['action']=I('act');
            $this->addRow('SeoRule', $data, array('url'=>U('index')));
        }else{
            $this->display('edit');
        }
    }
    
    /**
     * 假删除
     */
    public function del(){
        $id = I('ids');
        $this->verifyData($id);
        $map['id'] = array('in',$id);
        $this->delete('SeoRule', $map, array('url'=>U('index')));
    }
    
    /**
     * 还原
     */
    public function restore(){
        $ids=I('ids');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('in',$ids);
        parent::restore('SeoRule',$map,array('url'=>U('index')));
    }
    
    /**
     * 彻底删除
     */
    public function thoroughDel(){
        $this->doClear('SeoRule',I('ids'));
    }
    
    
    /**
     * 规则回收站
     */
    public function recycle(){
        $map = array('status' => -1);
        $list = $this->lists($this->SeoRuleModel, $map,'sort asc');
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('list', $list)->display();
    }
    
    /**
     * SEO排序
     */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');
            $pid = I('get.pid');
            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }else{
                if($pid !== ''){
                    $map['pid'] = $pid;
                }
            }
            $list = $this->SeoRuleModel->where($map)->field('id,title')->order('sort asc,id asc')->select();
            $this->meta_title = 'SEO排序';
            $this->column_name = 'title';
            $this->highlight_url=U('Seo/index');
            $this->assign('list',$list)->display('Public/sort');
        }elseif (IS_POST){
            $ids = explode(',', I('post.ids'));
            foreach ($ids as $key=>$value){
                $res = $this->SeoRuleModel->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功');
            }else{
                $this->error('排序失败');
            }
        }else{
            $this->error('非法请求');
        }
    }
}