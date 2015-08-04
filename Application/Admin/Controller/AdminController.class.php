<?php
namespace Admin\Controller;

/**
 * 后台公共控制器
 */

class AdminController extends \Common\Controller\BaseController{
    
    /**
     * 初始化方法
     */
    protected function _initialize(){
        define('UID',is_login());
        if( !UID ){ $this->redirect('Public/login'); }
        $this->getMenus();
    }
           
    /**
     * 获取菜单
     */
    protected function getMenus(){
        //获取系统菜单导航
        $map['status'] = array('gt',0);
        $SystemMenuModel = D('SystemMenu');
        $tree = new \Common\Util\Tree();
        $all_admin_menu_list = $tree->list_to_tree($SystemMenuModel->where($map)->select()); //所有系统菜单

        //设置数组key为菜单ID
        foreach($all_admin_menu_list as $key => $val){
            $all_menu_list[$val['id']] = $val;
        }

        //获取功能模块的后台菜单列表
        $moule_list = D('StoreModule')->where($map)->select(); //获取所有安装并启用的功能模块
        $all_module_menu_list = array();
        foreach($moule_list as $key => $val){
            $menu_list_item = $tree->list_to_tree(json_decode($val['admin_menu'], true));
            $all_module_menu_list[] = $menu_list_item[0];
        }

        //设置数组key为菜单ID
        foreach($all_module_menu_list as &$menu){
            $new_all_module_menu_list[$menu['id']] = $menu;
        }

        //合并系统核心菜单与功能模块菜单
        if($new_all_module_menu_list){
            $all_menu_list += $new_all_module_menu_list;
        }

        $current_menu = $SystemMenuModel->getCurrentMenu(); //当前菜单
        $parent_menu = $SystemMenuModel->getParentMenu($current_menu['id']); //获取面包屑导航
        foreach($parent_menu as $key => $val){
            $parent_menu_id[] = $val['id'];
        }
        $current_root_menu = $SystemMenuModel->getRootMenuById($current_menu['id']); //当前菜单的顶级菜单
        $side_menu_list = $all_menu_list[$current_root_menu['id']]['_child']; //左侧菜单

        $this->assign('__ALL_MENU_LIST__', $all_menu_list); //所有菜单
        $this->assign('__SIDE_MENU_LIST__', $side_menu_list); //左侧菜单
        $this->assign('__PARENT_MENU__', $parent_menu); //当前菜单的所有父级菜单
        $this->assign('__PARENT_MENU_ID__', $parent_menu_id); //当前菜单的所有父级菜单的ID
        $this->assign('__CURRENT_ROOTMENU__', $current_root_menu['id']); //当前主菜单
        $this->assign('__USER__', session('user_auth')); //用户登录信息
    }
}
