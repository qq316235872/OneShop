<?php
 
namespace Common\Model;
use Think\Model;
/**
 * 配置模型
 */
class SystemConfigModel extends Model{
    /**
     * 自动验证规则
     */
    protected $_validate = array(
        array('group', 'require', '配置分组不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('type', 'require', '配置类型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', 'require', '配置名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,32', '配置名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', '', '配置名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('title','require','配置标题必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '配置标题长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('title', '', '配置标题已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_BOTH),
    );

    /**
     * 获取配置列表
     * @return array 配置数组
     */
    public function lists(){
        $map['status']  = array('gt',0);
        $list = $this->where($map)->field('name,value,type')->select();
        foreach ($list as $key => $val){
            if($val['type'] === 'array'){ //数组类型需要解析配置的value
                $config[$val['name']] = parse_attr($val['value']);
            }else{
                $config[$val['name']] = $val['value'];
            }
        }
        return $config;
    }
}
