<?php
namespace Common\Controller;
use Think\Controller;

/**
 * 通用基控制器
 */
class BaseController extends Controller {
                        
        /**
         *  通用分页列表数据集获取方法
         *
         *  可以通过url参数传递where条件,例如:  userList.html?name=asdfasdfasdfddds
         *  可以通过url空值排序字段和方式,例如: userList.html?_field=id&_order=asc
         *  可以通过url参数r指定每页数据条数,例如: userList.html?r=5
         *
         * @param sting|Model  $model 模型名或模型实例
         * @param array        $where where查询条件(优先级: $where>$_REQUEST>模型设定)
         * @param array|string $order 排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
         *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
         *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
         *
         * @param array        $base 基本的查询条件
         * @param boolean      $field 单表模型用不到该参数,要用在多表join时为field()方法指定参数
         *
         * @return array|false
         * 返回数据集
         */
        final public function os_lists($model, $where = array(), $order = '', $base = array('status' => array('egt', 0)), $field = true){
            $options = array();
            $REQUEST = (array)I('request.');
            if (is_string($model)) {   $model = M($model);  }
            $OPT = new \ReflectionProperty($model, 'options');
            $OPT->setAccessible(true);
            $pk = $model->getPk();
            if ($order === null) {
            } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
                $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
            } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
                $options['order'] = $pk . ' desc';
            } elseif ($order) {
                $options['order'] = $order;
            }
            unset($REQUEST['_order'], $REQUEST['_field']);
            $options['where'] = array_filter(array_merge((array)$base,
                (array)$where), function ($val) {
                if ( $val === null) {
                    return false;
                } else {
                    return true;
                }
            });
            if (empty($options['where'])) {
                unset($options['where']);
            }
            $options = array_merge((array)$OPT->getValue($model), $options);
            $total = $model->where($options['where'])->count();
            if (isset($REQUEST['r'])) {
                $listRows = (int)$REQUEST['r'];
            } else {
                $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
            }
            $page = new \Think\Page($total, $listRows, $REQUEST);
            if ($total > $listRows) {
                $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            }
            $p = $page->show();
            $this->assign('_page', $p ? $p : '');
            $this->assign('_total', $total);
            $options['limit'] = $page->firstRow . ',' . $page->listRows;
            $model->setProperty('options', $options);
            return $model->field($field)->select();
        }
	

	/**
	 * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
	 *
	 * @param string $model 模型名称,供M函数使用的参数
	 * @param array  $data  修改的数据
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	final public function os_edits ( $model ,$data, $where , $msg ){
            $id    = array_unique((array)I('id',0));
            $id    = is_array($id) ? implode(',',$id) : $id;
            $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
            $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
            if( M($model)->where($where)->save($data)!==false ) {
                    $this->success($msg['success'],$msg['url'],$msg['ajax']);
            }else{
                    $this->error($msg['error'],$msg['url'],$msg['ajax']);
            }
	}

	/**
	 * 设置一条或者多条数据的状态
	 */
	final public function os_set_status($Model=CONTROLLER_NAME,$idName='id'){
            $ids    =   I('request.ids');
            $status =   I('request.status');
            if(empty($ids)){
                    $this->error('请选择要操作的数据');
            }
            $map[ $idName ] = array('in',$ids);
            switch ($status){
                    case -1 :
                            $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));
                            break;
                    case 0  :
                            $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                            break;
                    case 1  :
                            $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
                            break;
                    default :
                            $this->error('参数错误');
                            break;
            }
	}
	
	
	/**
	 * 禁用条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的 where()方法的参数
	 * @param array  $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	final public function os_forbid ( $model , $where = array() , $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！')){
		$data    =  array('status' => 0);
		$this->os_edits( $model , $data, $where, $msg);
	}
	
	/**
	 * 恢复条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	final public function os_resume (  $model , $where = array() , $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！')){
		$data    =  array('status' => 1);
		$this->os_edits(   $model , $data, $where, $msg);
	}
	
	/**
	 * 还原条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	final public function os_restore (  $model , $where = array() , $msg = array( 'success'=>'状态还原成功！', 'error'=>'状态还原失败！')){
		$data    = array('status' => 1);
		$where   = array_merge(array('status' => -1),$where);
		$this->os_edits(   $model , $data, $where, $msg);
	}
	
	/**
	 * 条目假删除
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	final public function os_delete ( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！')) {
		$data['status']         =   -1;
		$this->os_edits(   $model , $data, $where, $msg);
	}
                        
	
	/**
	 * 增改通用
	 */
	final public function os_add_or_edit( $Model ,$messageArr=array('新增','修改'),$jump=1){
		$ret = 0;
		$Pk = $Model->getPk();
		if( $Model->create() ){
			if( IS_POST ){
                            if( $_POST[$Pk] ){
                                    $ret = $Model->save();
                                    $message = $messageArr[1];
                            }else{
                                    $ret = $Model->add();
                                    $message = $messageArr[0];
                            }
			}
		}else{
			$this->error( $Model->getError() ); exit;
		}
		return $this->os_redirect( $ret, $message, $jump );
	}
        
	/**
	 * 删除通用
	 */
	final public function os_true_delete( $Model , $map, $message='删除',$jump=1){
		if( !is_array($map) ){
			$Pk = $Model->getPk();
			$map = array($Pk=>$map);
		}
		$ret = $Model->where( $map )->delete();
		return $this->os_redirect( $ret, $message, $jump );
	}
        
	/**
	 * 跳转通用
	 */
	final public function os_redirect( $ret, $message='操作', $jump=1 ){
		if( $jump || $this->jumpUrl ){
			$this->jumpUrl = $this->jumpUrl?$this->jumpUrl:'';
			if( $ret ){
				$this->success( $message.'成功', $this->jumpUrl );
			}else{
				$this->error( $message.'失败' );
			}
		}else{
			return $ret;
		}
	}
}
