<?php
namespace Common\Model;

use Think\Model;

class GoodsAttributeModel extends Model
{
    /* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '属性名称必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
    
    /* 获取属性数量 */
    public function getGoodsAttributeNum($type_id=0){
        if($type_id==0){
            return $this->count();
        }else{
            return $this->where(array('type_id'=>$type_id))->count();
        }
    }
    /* 获取属性值数量 */
    public function getGoodsAttributeValueNum($id=0){
  
        return $this->where(array('pid'=>$id))->count();
    }
    
    /* 获取属性详情 */
    public function getGoodsAttributeInfo($id=0){
        
        return $this->where(array('id'=>$id))->find();
        
    }
    /* 如果为可选值录入则获取属性可选值列表 */
    public function getGoodsAttributeOptionalList($id=0){
        $info=$this->field(TRUE)->where(array('id'=>$id))->find();
        if($info['field_entering']){
            return preg_split('/[,;\r\n]+/', trim($info['optional_value'], ",;\r\n"));
        }else{
            return FALSE;
        }
    }
    
    /* 格式化属性数据 */
    public function formattingAttributeList($list){
        for($i=0;$i<count($list);$i++){
            $list[$i]['attribute_num']=$this->getGoodsAttributeNum($list[$i]['id']);
        }
        return $list;
    }
    /* 格式化属性值数据 */
    public function formattingAttributeValueList($list){
        for($i=0;$i<count($list);$i++){
            $list[$i]['attribute_value_num']=$this->getGoodsAttributeValueNum($list[$i]['id']);
        }
        return $list;
    }
    
    
}