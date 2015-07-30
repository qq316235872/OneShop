<?php
namespace Common\Payment;

/**
 * 余额付款
 */
class BalancePayment extends BasePayment{
		
	//初始化
	public function _initialize(){
		//$this->order_sn_name = 'out_trade_no';	//订单号名称
	}
		
	//支付方式的基本信息
	protected  function baseInfo(){
		return array(
				'pay_name'		=>	'余额付款',					
				'pay_code'		=>	'balance',					
				'pay_desc'		=>	'用户账户余额需要大于订单金额方可进行',
				'is_cod'		=>	0,							
				'is_online'		=>	1,							
				'author'		=> 	'OneShop',					
				'version'		=>	'1.0.0',					
				'paymentwebsite'=>	'http://52software.com/'		
		);
	}
	//配置信息
	public function config(){
		return array();
	}
	
	
	
	//
	public function getPayCode( $order ){
		
		
		//检测用户余额是否足以支付
		$user_money = D('AccountLog')->get_user_money( $order['uid'] );
				
		//不足，提示，并终止后续执行
		if( $user_money<$order['order_amount'] ){
			$ret['noticeInfo'] = array(
					'status'	=> '0',				//付款失败
					'title'		=> '订单提交成功',
					'sub_title'	=> '订单提交成功，余额不足，请及时付款。',
					'contents'	=> '您的订单已成功提交！<a href="'.U('Order/orderlist').'">查看订单详情</a>',
			);
			return $ret;
		}
		//echo $user_money;vde( $order );
		
		//足以，直接从用户账户余额中减去订单总额（添加余额使用日志）
		
		
		//修改订单付款状态等信息
		$order['pay_status'] = 2;
		$order['action_note'] = '订单：'.$order['order_sn'].',使用余额付款';
		D('OrderInfo')->updateOrderInfo( $order );		
		D('AccountLog')->get_user_money( $order['uid'],true );	//充值用户表的订单余额
		
		//提示用户付款成功
		$ret['noticeInfo'] = array(
				'status'	=> '1',				//成功
				'title'		=> '订单提交成功',
				'sub_title'	=> '订单提交成功',
				'contents'	=> '您的订单已成功提交！<a href="'.U('Order/orderlist').'">查看订单详情</a>',
		);
		return $ret;
	}
	
	//
	public function callback(){
			
	}
	
}
