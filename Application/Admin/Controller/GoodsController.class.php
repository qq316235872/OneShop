<?php
namespace Admin\Controller;
/**
 * 后台商品模块相关操作管理控制器
 * @author 逆水行舟丶 <316235872@qq.com>
 */
class GoodsController extends AdminController {
    Public $GoodsAttributeModel;
    Public $GoodsBrandModel;
    Public $GoodsCategoryModel;
    Public $GoodsTypeModel;
    Public function _initialize() {
        parent::_initialize();
        $this->GoodsAttributeModel=D('Common/GoodsAttribute');
        $this->GoodsBrandModel=D('Common/GoodsBrand');
        $this->GoodsCategoryModel=D('Common/GoodsCategory');
        $this->GoodsTypeModel=D('Common/GoodsType');
        $this->meta_title = '商品管理';
    }
    /**
     * 商品品牌列表
     */
    public function goodsBrandList(){
        $map = array('status' => array('EGT', 0));
        $list = $this->lists($this->GoodsBrandModel, $map,'sort asc');
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('list', $list)->display();
 
    }
    
    
    
    /**
     * 编辑商品品牌信息
     */
    public function goodsBrandEdit($id = 0){
        if(IS_POST){
            $data=$this->GoodsBrandModel->create();
            if($data){
                $this->editRow('GoodsBrand',$data,NULL,array('url'=>U('goodsBrandList')));
            }else{
                $this->error($this->GoodsBrandModel->getError());
            }
        } else {
                $this->assign('info', $this->GoodsBrandModel->field(true)->find($id))->display();
        }
    }
    
    
    /**
     * 设置品牌状态
     */
    public function updateGoodsBrandStatus(){
         $this->setStatus('GoodsBrand');
    }
    
    /**
     * 添加商品品牌信息
     */
    public function goodsBrandAdd(){
        if(IS_POST){
            $data=$this->GoodsBrandModel->create();
            if($data){
                $this->addRow('GoodsBrand', $data, array('url'=>U('goodsBrandList')));
            }else{
                $this->error($this->GoodsBrandModel->getError());
            }
        }else{
            $this->display('goodsBrandEdit');
        }
    }
    

    
    
    /**
     * 商品类型列表
     */
    public function goodsTypeList(){
        $map = array('status' => array('EGT', 0));
        $list=$this->GoodsAttributeModel->formattingAttributeList($this->lists($this->GoodsTypeModel, $map,'sort asc'));
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('list',$list)->display();
    }
    
    
    
    /**
     * 编辑商品类型信息
     */
    public function goodsTypeEdit($id = 0){
        if(IS_POST){
            $data=$this->GoodsTypeModel->create();
            if($data){
                $this->editRow('GoodsType',$data,NULL,array('url'=>U('goodsTypeList')));
            }else{
                $this->error($this->GoodsTypeModel->getError());
            }
        } else {
                $this->assign('info', $this->GoodsTypeModel->field(true)->find($id))->display();
        }
    }
    
    
    /**
     * 设置类型状态
     */
    public function updateGoodsTypeStatus(){
         $this->setStatus('GoodsType');
    }
    
    /**
     * 添加商品类型信息
     */
    public function goodsTypeAdd(){
        if(IS_POST){
            $data=$this->GoodsTypeModel->create();
            if($data){
                $this->addRow('GoodsType', $data, array('url'=>U('goodsTypeList')));
            }else{
                $this->error($this->GoodsTypeModel->getError());
            }
        }else{
            $this->display('goodsTypeEdit');
        }
    }
    
    /**
     * 商品类型属性列表
     */
    public function goodsAttributeList($id=0){
            $map = array('status' => array('EGT', 0) , 'type_id' => $id);
            $list = $this->GoodsAttributeModel->formattingAttributeValueList($this->lists($this->GoodsAttributeModel, $map,'sort asc'));
            Cookie('__forward__',$_SERVER['REQUEST_URI']);
            $info=$this->GoodsTypeModel->getGoodsTypeInfo($id);
            $this->type_name=$info['name'];
            $this->type_id=$id;
            $this->assign('list', $list)->display();
    }
    
    /**
     * 编辑商品类型属性信息
     */
    public function checkEnteringValue($info){
        if($info['field_entering']==1){
            if($info['optional_value']==''){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * 编辑商品类型属性信息
     */
    public function goodsAttributeEdit($id = 0){
        if(IS_POST){
            $data=$this->GoodsAttributeModel->create();
            if($this->checkEnteringValue($data)){
                $this->error('请输入可选值');
            }
            if($data){
                $this->editRow('GoodsAttribute',$data,NULL,array('url'=>U('goodsAttributeList',array('id'=>$data['type_id']))));
            }else{
                $this->error($this->GoodsAttributeModel->getError());
            }
        } else {
                $map = array('status' => array('EGT', 0));
                $this->type_list = $this->lists($this->GoodsTypeModel, $map,'sort asc');
                $this->assign('info', $this->GoodsAttributeModel->field(true)->find($id))->display();
        }
    }
    
    
    /**
     * 设置商品属性状态
     */
    public function updateGoodsAttributeStatus(){
         $this->setStatus('GoodsAttribute');
    }
    
    /**
     * 添加商品属性信息
     */
    public function goodsAttributeAdd($id=0){
        if(IS_POST){
            $data=$this->GoodsAttributeModel->create();
            if($this->checkEnteringValue($data)){
                $this->error('请输入可选值');
            }
            if($data){
                $this->addRow('GoodsAttribute', $data, array('url'=>U('goodsAttributeList',array('id'=>$data['type_id']))));
            }else{
                $this->error($this->GoodsAttributeModel->getError());
            }
        }else{
            $map = array('status' => array('EGT', 0));
            $this->type_list = $this->lists($this->GoodsTypeModel, $map,'sort asc');
            $this->assign('type_id',$id)->display('goodsAttributeEdit');
        }
    }
    
    /**
     * 商品属性值列表
     */
    public function goodsAttributeValueList($id=0,$go=0){
            $map = array('status' => array('EGT', 0) , 'pid' => $id);
            $list = $this->lists($this->GoodsAttributeModel, $map,'sort asc');
            Cookie('__forward__',$_SERVER['REQUEST_URI']);
            $a_info=$this->GoodsAttributeModel->getGoodsAttributeInfo($id);
            $this->attribute_name=$a_info['name'];
            $t_info=$this->GoodsTypeModel->getGoodsTypeInfo($a_info['type_id']);
            $this->type_name=$t_info['name'];
            $this->pid=$id;
            $this->go=$go;
            $this->assign('list', $list)->display();
    }
    
    
    /**
     * 添加商品属性值
     */
    public function goodsAttributeValueAdd($id=0){
        if(IS_POST){
            $data=$this->GoodsAttributeModel->create();
            if($data){
                $this->addRow('GoodsAttribute', $data, array('url'=>U('goodsAttributeValueList',array('id'=>$data['pid']))));
            }else{
                $this->error($this->GoodsAttributeModel->getError());
            }
        }else{
            $this->entering_res=$this->GoodsAttributeModel->getGoodsAttributeOptionalList($id);
            $info=$this->GoodsAttributeModel->getGoodsAttributeInfo($id);
            $this->attribute_name=$info['name'];
            $this->assign('pid',$id)->display('goodsAttributeValueEdit');
        }
    }
 
    /**
     * 编辑商品属性值
     */
    public function goodsAttributeValueEdit($id = 0){
        if(IS_POST){
            $data=$this->GoodsAttributeModel->create();
            if($data){
                $this->editRow('GoodsAttribute',$data,NULL,array('url'=>U('goodsAttributeValueList',array('id'=>$data['pid']))));
            }else{
                $this->error($this->GoodsAttributeModel->getError());
            }
        } else {
            $info=$this->GoodsAttributeModel->field(true)->find($id);
            $f_info=$this->GoodsAttributeModel->getGoodsAttributeInfo($info['pid']);
            $this->entering_res=$this->GoodsAttributeModel->getGoodsAttributeOptionalList($f_info['id']);
            $this->attribute_name=$f_info['name'];
            $this->assign('info', $info)->display();
        }
    }
    
    /**
     * 分类列表
     */
    public function goodsCategoryList(){
        $tree = $this->GoodsCategoryModel->getTree(0,'id,name,sort,pid,status');
        $this->assign('tree', $tree);
        C('_SYS_GET_CATEGORY_TREE_', true); //标记系统获取分类树模板
        $this->meta_title = '分类管理';
        $this->display();
    }
    
    
    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function tree($tree = null){
        C('_SYS_GET_CATEGORY_TREE_') || $this->_empty();
        $this->assign('tree', $tree);
        $this->display('tree');
    }
    
    /* 编辑分类 */
    public function goodsCategoryEdit($id = null, $pid = 0){
        if(IS_POST){ //提交表单
            if($data=$this->GoodsCategoryModel->create()){
                if($this->GoodsCategoryModel->save($data)){
                   $this->success('编辑成功！', U('goodsCategoryList')); 
                }else{
                   $this->error('编辑失败！');
                }
                
            } else {
                $this->error($this->GoodsCategoryModel->getError());
            }
        } else {
            $cate = '';
            if($pid){
                /* 获取上级分类信息 */
                $cate = $this->GoodsCategoryModel->info($pid, 'id,name,status');
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级分类不存在或被禁用！');
                }
            }

            /* 获取分类信息 */
            $info = $id ? $this->GoodsCategoryModel->info($id) : '';
            $this->assign('info',       $info);
            $this->assign('category',   $cate);
            $this->meta_title = '编辑分类';
            $this->display();
        }
    }
    
    
    /* 新增分类 */
    public function goodsCategoryAdd($pid = 0){
        if(IS_POST){ //提交表单
            if($data=$this->GoodsCategoryModel->create()){
                if($this->GoodsCategoryModel->add($data)){
                   $this->success('新增成功！', U('goodsCategoryList')); 
                }else{
                   $this->error('新增失败！');
                }
                
            } else {
                $this->error($this->GoodsCategoryModel->getError());
            }
        } else {
            $cate = array();
            if($pid){
                $cate = $this->GoodsCategoryModel->info($pid, 'id,name,status');
                if(!($cate && 1 == $cate['status']))  $this->error('指定的上级分类不存在或被禁用！');
            }
            $this->assign('category', $cate);
            $this->meta_title = '新增分类';
            $this->display('goodsCategoryEdit');
        }
    }
    
    /**
     * 设置状态
     */
    public function goodsCategorySetStatus(){
         $this->setStatus('GoodsCategory');
    }
    
    /**
     * 删除一个分类
     * @author 逆水行舟丶 <316235872@qq.com>
     * @todo 判断该分类下有没有商品
     */
    public function goodsCategoryRemove(){
        $cate_id = I('id');
        if(empty($cate_id)){
            $this->error('参数错误!');
        }

        //判断该分类下有没有子分类，有则不允许删除
        $child = $this->GoodsCategoryModel->where(array('pid'=>$cate_id))->field('id')->select();
        if(!empty($child)){
            $this->error('请先删除该分类下的子分类');
        }
        //删除该分类信息
        $res = $this->GoodsCategoryModel->delete($cate_id);
        if($res !== false){
            $this->success('删除分类成功！');
        }else{
            $this->error('删除分类失败！');
        }
    }
    
    
}