<?php
namespace Common\Payment;

/**
 * 货到付款
 */
class CodPayment extends BasePayment{
	
	//支付方式的基本信息
	protected  function baseInfo(){
		return array(
				'pay_name'		=>	'货到付款',					
				'pay_code'		=>	'cod',					
				'pay_desc'		=>	'开通城市：×××,货到付款区域：×××',
				'is_cod'		=>	1,							
				'is_online'		=>	0,							
				'author'		=> 	'OneShop',					
				'version'		=>	'1.0.0',					
				'paymentwebsite'=>	'http://52software.com/'		
		);
	}
	//配置信息
	public function config(){
		return array();
	}
	
	
	
	
	public function getPayCode(){
		
		$ret['noticeInfo'] = array(
				'status'	=> '1',				//成功
				'title'		=> '订单提交成功',
				'sub_title'	=> '订单提交成功',
				'contents'	=> '您的订单已成功提交！<a href="'.U('Order/orderlist').'">查看订单详情</a>',
		);
		
		return $ret;
	}
	

	public function _initialize(){
		$this->code = 'cod';
	}
}
