<?php

namespace Admin\Controller;
/**
 * 系统配置控制器
 */
class SystemConfigController extends AdminController{
    /**
     * 配置列表
     * @param $tab 配置分组ID
     */
    public function index($tab = 1){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|name|title'] = array($condition, $condition, $condition,'_multi'=>true);
        //获取所有配置
        $map['status'] = array('egt', 0); //禁用和正常状态
        $map['group'] = array('eq', $tab);
        $data=$this->os_lists('system_config', $map, 'sort asc,id asc');
        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->title('配置列表')  //设置页面标题
                ->addNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/配置名称/配置标题', U('index', array('tab' => $tab)))
                ->setTablist(C('CONFIG_GROUP_LIST')) //设置Tab按钮列表
                ->setCurrentTab($tab) //设置当前Tab
                ->addField('id', 'ID', 'text')
                ->addField('name', '名称', 'text')
                ->addField('title', '标题', 'text')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data['list'])    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->setPage($data['page']->show())
                ->display();
    }

    /**
     * 新增配置
     */
    public function add(){
        if(IS_POST){
            $this->add_or_edit();
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->title('新增配置')  //设置页面标题
                    ->setUrl(U('add')) //设置表单提交地址
                    ->addItem('group', 'select', '配置分组', '配置所属的分组', C('CONFIG_GROUP_LIST'))
                    ->addItem('type', 'select', '配置类型', '配置类型的分组', C('FORM_ITEM_TYPE'))
                    ->addItem('name', 'text', '配置名称', '配置名称')
                    ->addItem('title', 'text', '配置标题', '配置标题')
                    ->addItem('value', 'textarea', '配置值', '配置值')
                    ->addItem('options', 'textarea', '配置项', '如果是单选、多选、下拉等类型 需要配置该项')
                    ->addItem('tip', 'textarea', '配置说明', '配置说明')
                    ->addItem('sort', 'num', '排序', '用于显示的顺序')
                    ->display();
        }
    }
    
    
    /**
     * 添加或编辑配置
     */
    public function add_or_edit(){
        S('DB_CONFIG_DATA',null);
        $this->jumpUrl = U('index');
        $this->os_add_or_edit(D('SystemConfig'));
    }

    /**
     * 编辑配置
     */
    public function edit($id){
        if(IS_POST){
            $this->add_or_edit();
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->title('编辑配置')  //设置页面标题
                    ->setUrl(U('edit')) //设置表单提交地址
                    ->addItem('id', 'hidden', 'ID', 'ID')
                    ->addItem('group', 'select', '配置分组', '配置所属的分组', C('CONFIG_GROUP_LIST'))
                    ->addItem('type', 'select', '配置类型', '配置类型的分组', C('FORM_ITEM_TYPE'))
                    ->addItem('name', 'text', '配置名称', '配置名称')
                    ->addItem('title', 'text', '配置标题', '配置标题')
                    ->addItem('value', 'textarea', '配置值', '配置值')
                    ->addItem('options', 'textarea', '配置项', '如果是单选、多选、下拉等类型 需要配置该项')
                    ->addItem('tip', 'textarea', '配置说明', '配置说明')
                    ->addItem('sort', 'num', '排序', '用于显示的顺序')
                    ->setFormData(D('SystemConfig')->find($id))
                    ->display();
        }
    }

    /**
     * 获取某个分组的配置参数
     */
    public function group($tab = 1){
        //根据分组获取配置
        $map['status'] = array('egt',0); //禁用和正常状态
        $map['group'] = array('eq', $tab);
        $data_list = D('SystemConfig')->where($map)->order('sort asc,id asc')->select();

        //构造表单名、解析options
        foreach($data_list as &$data){
            $data['name'] = 'config['.$data['name'].']';
            $data['options'] = parse_attr($data['options']);
        }

        //使用FormBuilder快速建立表单页面。
        $builder = new \Common\Builder\FormBuilder();
        $builder->title('系统设置')  //设置页面标题
                ->setTablist(C('CONFIG_GROUP_LIST')) //设置Tab按钮列表
                ->setCurrentTab($tab) //设置当前Tab
                ->setUrl(U('groupSave')) //设置表单提交地址
                ->setExtraItems($data_list) //直接设置表单数据
                ->display();
    }

    /**
     * 批量保存配置
     */
    public function groupSave($config){
        if($config && is_array($config)){
            $config_object = D('SystemConfig');
            foreach ($config as $name => $value){
                $map = array('name' => $name);
                $config_object->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
        $this->os_redirect(true,'保存');
    }
}
