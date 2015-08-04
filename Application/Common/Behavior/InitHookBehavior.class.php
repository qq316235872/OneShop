<?php

namespace Common\Behavior;
use Think\Behavior;
use Think\Hook;
defined('THINK_PATH') or exit();
/**
 * 初始化钩子信息
 */
class InitHookBehavior extends Behavior{
    /**
     * 行为扩展的执行入口必须是run
     */
    public function run(&$content){
        $data = S('hooks');
        if(!$data){
            $hooks = D('AddonHook')->getField('name,addons');
            foreach($hooks as $key => $value){
                if($value){
                    $map['status']  =   array('gt',0);
                    $names          =   explode(',',$value);
                    $map['name']    =   array('IN',$names);
                    $data = D('Addon')->where($map)->getField('id,name');
                    if($data){
                        $addons = array_intersect($names, $data);
                        Hook::add($key, array_map('get_addon_class', $addons));
                    }
                }
            }
            S('hooks', Hook::get());
        }else{
            Hook::import($data,false);
        }
    }
}
