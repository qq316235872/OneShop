<?php

namespace Common\Behavior;
use Think\Behavior;
defined('THINK_PATH') or exit();
/**
 * 根据不同情况读取数据库的配置信息并与本地配置合并
 */
class InitConfigBehavior extends Behavior{
    /**
     * 行为扩展的执行入口必须是run
     */
    public function run(&$content){
        //读取数据库中的配置
        $config = S('DB_CONFIG_DATA');
        if(!$config){
            
            //获取所有系统配置
            $config = D('SystemConfig')->lists();
            
            if($this->is_theme()){
                $config_public = __ROOT__.'/Public';
                $config_img    = __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/img';
                $config_css    = __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/css';
                $config_js     = __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/js';
                $config['DEFAULT_THEME'] = '';
            }else{
                $config_public = __ROOT__.'/Public';
                $config_img    = __ROOT__.'/Application/'.MODULE_NAME.'/View/'.$config['DEFAULT_THEME'].'/Public/img';
                $config_css    = __ROOT__.'/Application/'.MODULE_NAME.'/View/'.$config['DEFAULT_THEME'].'/Public/css';
                $config_js     = __ROOT__.'/Application/'.MODULE_NAME.'/View/'.$config['DEFAULT_THEME'].'/Public/js';
            }
            $config['TMPL_PARSE_STRING']['__PUBLIC__'] = $config_public;
            $config['TMPL_PARSE_STRING']['__IMG__']    = $config_img;
            $config['TMPL_PARSE_STRING']['__CSS__']    = $config_css;
            $config['TMPL_PARSE_STRING']['__JS__']     = $config_js;
            $config['DATA_CACHE_PREFIX'] = MODULE_NAME.'_'; //缓存前缀
            $config['SESSION_PREFIX']    = MODULE_NAME.'_'; //Session前缀
            $config['COOKIE_PREFIX']     = MODULE_NAME.'_'; //Cookie前缀
            S('DB_CONFIG_DATA', $config, 3600); //缓存配置
        }

        if($this->is_theme()){ $config['DEFAULT_THEME'] = ''; }
        C($config); //添加配置
    }
    
    public function is_theme(){
        if(MODULE_NAME === 'Admin' || MODULE_NAME === 'Install' ){
            return true;
        }else{
            return false;
        }
    }
}
