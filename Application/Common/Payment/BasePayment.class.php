<?php
namespace Common\Payment;
use Think\Model;

/**
 * 支付方式模型逻辑层公共模型
 * 所有逻辑层模型都需要继承此模型
 */
class BasePayment extends Model{
	
	public $order_sn_name = 'order_sn';	//支付工具回调时订单号的名称
	
	//返回订单号名称
	public function getOrderSn( $getValue=false ){
		if( $getValue ){
			return $this->getOrderSnValue();
		}else{
			return $this->order_sn_name;
		}
	}
	
	//返回订单号
	public function getOrderSnValue(){
		return I( $this->order_sn_name );
	}
	
	//支付成功回调
	public function paySuccess(){
		echo 'success';
	}
	
	//支付失败回调
	public function payError(){
		echo 'error';
	}
	
	//获取支付方式的基础信息
	public function get_baseInfo(){
		//基础信息
	 	$baseInfo = $this->baseInfo();
	 	
	 	//组件默认值
	 	$baseInfo['is_installed'] 	= 0;	//未安装
	 	$baseInfo['enabled'] 		= 0;	//未启用
	 	
	 	//配置信息
	 	$baseInfo['config']	= $this->config();
	 	
	 	//vd($baseInfo);
	 	return $baseInfo;
	}
	
	//获取配置信息
	public function get_config(){

		$map = array('pay_code'=>$this->get_code());
		
		$pay_config = D('Payment')->where($map)->getField('pay_config');
		
		return unserialize($pay_config);
	}
	
	//获取支付标识
	public function get_code(){
		$config = $this->baseInfo();
		
		return $config['pay_code'];
	}
 
	
	//支付方式的基本信息————不同帐号保持一致
	protected function baseInfo(){
		return array(
				'pay_name'		=>	'支付工具名称',			//支付工具名称
				'pay_code'		=>	'#',					//支付英文标识
				'pay_desc'		=>	'支付描述',				//支付描述
				'is_cod'		=>	0,						//货到付款
				'is_online'		=>	0,						//在线支付
				'author'		=> 	'#',					//插件作者
				'version'		=>	'0',					//插件版本
				'paymentwebsite'=>	'#',					//支付方式网址
		);
	}
		
	//需要填充的配置————根据具体的支付工具变更
	//需要填充的配置
	public function config(){
		return array(
				array(
						'name' 		=> 'account',
						'title'		=> '支付帐户',
						'remark'	=> '必填',
						'is_show'   => '1',
						'type' 		=> 'text',
						'value' 	=> ''
				),
				array(
						'name' 		=> 'key',
						'title'		=> '交易安全校验码',
						'remark'	=> '必填',
						'is_show'   => '1',
						'type' 		=> 'text',
						'value' 	=> ''
				),
				array(
						'name' 		=> 'partner_id',
						'title'		=> '合作者身份ID',
						'remark'	=> '必填',
						'is_show'   => '1',
						'type' 		=> 'text',
						'value' 	=> ''
				),
		);
	}

	/**
	 * 生成支付代码
	 * @param array $order	订单信息
	 */
	public function getPayCode( $order ){
		
		return 'Pay Code...';
	}
	
	//手机端支付
	public function getPayCode_Mobile( $order ){
		$this->getPayCode( $order );	//默认调用PC端的支付	
	}
	
	/**
	 * 到支付工具提供方查询订单信息
	 * @param unknown $oInfo	订单信息
	 * @return string
	 */
	public function order_query($oInfo){
		
		return 'order_query_api is not develop...';
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
