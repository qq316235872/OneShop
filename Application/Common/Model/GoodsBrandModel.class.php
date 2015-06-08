<?php
namespace Common\Model;

use Think\Model;

class GoodsBrandModel extends Model
{
   
    /* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '品牌名称必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '', '品牌名称已存在', self::EXISTS_VALIDATE, 'unique'),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
 
}