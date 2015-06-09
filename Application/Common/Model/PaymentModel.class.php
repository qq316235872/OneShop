<?php
namespace Common\Model;
use Think\Model;
/**
 *支付模型
 */
class PaymentModel extends BaseModel{
	
	
	/**
	 * 获取支付组建目录中存在的组建文件数据列表
	 * @param string $pay_code	需要查询的组件名称，默认为空。即查询所有的组件
	 * @return multitype:组件信息数组
	 */
	public function getModule( $pay_code=null ){
		
		//所有支付组件的名称数组
		$paymentList = $this->getPaymentNameList(false);
                 
		//获取各组件的基础信息
		$list = array();
		$count = count( $paymentList );
		for( $i=0;$i<$count;$i++ ){
			$payName 	= $paymentList[$i];
			$Pay = D($payName,'Payment');
			if( !$pay_code ){													//查询所有的支付组件
				$list[] = $Pay->get_baseInfo();
			}elseif( strtolower($pay_code)==strtolower($payName) ){				//查询指定的支付组件
				$list = $Pay->get_baseInfo();
				break;
			}
		}	
		return $list;
	}
	
	//获取支付方式名称数组
	public function getPaymentNameList($createObj=true){
		//系统所有可用支付组件
		$allFile 	= file_list( C('PAYMENT_PATH') );
		$list 		= array();
		$count 		= count( $allFile );
		for( $i=0;$i<$count;$i++ ){
			$fileName 	= $allFile[$i];			
			if( !strpos($fileName, 'Payment') )continue;
			
			$payName 	= str_replace('Payment.class.php', '', $fileName);
			$payName	= ucfirst( strtolower($payName) );
			if( $payName != 'Base' ){
				$list[] = $createObj?D($payName,'Payment'):$payName;
			}
		}
		return $list;
	}
	
	/**
	 * 获取所有支付组件的订单名称数组
	 * @param string $getOrderSnValue 是否为遍历支付组件直接返回订单号内容
	 * @return multitype:NULL
	 */
	public function getAllPaymentOrderSnName(){
		$paymentList = $this->getPaymentNameList();
		$list = array();
		
		//系统默认订单号，优先级最高
		$list[] = D('Base','Payment')->getOrderSn();//order_sn_name;
		
		$count = count( $paymentList );
		for( $i=0;$i<$count;$i++ ){
			$Pay 	= $paymentList[$i];
			//去重存储
			if( !in_array($Pay->order_sn_name,$list) )
				$list[] = $Pay->order_sn_name;
		}

		return $list;
	}
	
	//获取订单号：用于支付回调时不确定回调返回信息格式等情况下遍历支付组件，按其特定方式获取
	public function getOrderSn(){
		$paymentList 	= $this->getPaymentNameList();
		
		//vd($paymentList);
		
		$count 			= count( $paymentList );
		for( $i=0;$i<$count;$i++ ){
			$orderSn 	= $paymentList[$i]->getOrderSnValue();
			//去重存储
			if( $orderSn ){
				return $orderSn;
				break;
			}
		}
		return null;
	}
	
	
	//获取外部传入的订单号
	
	
	/**
	 * 将支付组件与本地安装的支付工具拼装
	 * @param array $installedPayment	已安装的支付组件
	 */
	public function combination( $installedPayment ){
           
		//系统所有可使用的支付组件
		$allPayment =	$this->getModule();	
		//将已安装的组件数组转换成以组件支付标识为键的关联数组
		$installedPayment = getIdIndexArr($installedPayment,'pay_code');
		$retPayments = array();
		
		//将已安装
		foreach ($allPayment as $payment){
			//属性替换，已安装的属性以已安装的为准
			if( isset($installedPayment[ $payment['pay_code'] ]) ){			//已安装
				$payment = array_merge($payment,$installedPayment[ $payment['pay_code'] ]);
				$payment['is_installed'] = 1;
			}else{		//未安装
				$payment = $this->format($payment);
			}
			$retPayments[] = $payment;
		}
		
		return $retPayments;
	}
	
	//获取支付详细信息
	public function getInfo( $map ){
		if( is_array($map) ){		//数组查询
			$info = $this->where( $map )->find();
		}else{						//主键检索
			$Pk = $this->getPk();
			$info = $this->where( array($Pk=>$map) )->find();
		}
		if( !$info ){	
			//组件未安装，到足迹目录查取组件信息
			$info = $this->getModule($map['pay_code']);
		}else{
			//组件
			$Pay = D(ucfirst($map['pay_code']),'Payment');
			$config = $Pay->config();
			
			$db_config = $info['pay_config'] = unserialize( $info['pay_config'] );
			foreach($config as $key=>$value){
				$config[$key]['value'] = $db_config[ $value['name'] ];
			}
			$info['config'] = $config;
		}
		return $info;
	}
	
	//单条支付数据格式化
	public function format( $info ){
		//格式化收费标准
		$info['pay_fee_norm'] = 0;
		switch( $info['pay_fee_type'] ){
			case 1:		//百分比
				$info['pay_fee_norm'] = $info['pay_fee_content'].'%';		//显示
				$info['pay_fee_operation'] = $info['pay_fee_content']/100;
				break;
			case 2:		//固定值
				$info['pay_fee_norm'] = $info['pay_fee_content'].'RMB';
				$info['pay_fee_operation'] = $info['pay_fee_content'];
				break;
		}
		//
		$PAY_FEE_TYPE = C('PAY_FEE_TYPE');
		$info['pay_fee_explain'] = $PAY_FEE_TYPE[$info['pay_fee_type']]['explain'];
		//是否
		$YES_NO = C('YES_NO');
		$info['enabled_name'] = $YES_NO[ $info['enabled'] ];
		$info['is_online_name'] = $YES_NO[ $info['is_online'] ];
		

		return $info;
	}

	

	//获取订单所需的支付费用
	public function getFee( $order_amount_no_pay_fee, $pay_id ){
		//获取支付信息
		$info = $this->where( array('pay_id'=>$pay_id) )->find();
		$info = $this->format( $info );
		//vd($info);
		//计算返值
		switch( $info['pay_fee_type'] ){
			case 0:			//免费
				return 0;
				break;
			case 1:			//百分比
				//订单总额
				return $order_amount_no_pay_fee * $info['pay_fee_operation'];
				break;
			case 2:			//固定金额
				return $info['pay_fee_operation'];
				break;
		}
	}
	
	
	//===================================================================================
	//支付处理
	public function do_pay( $oInfo ){

		//已成功提交的订单无需后续执行
		if( $oInfo['pay_status']==2 ){	
			$ret['noticeInfo'] = array(
					'status'	=> '1',				//成功
					'title'		=> '订单提交成功',
					'sub_title'	=> '订单提交成功',
					'contents'	=> '您的订单已成功提交！<a href="'.U('Order/orderlist').'">查看订单详情</a>',
			);
			return $ret;
		}
		
		//查询出订单付款方式
		$payCode 	= $this->where( array('pay_id'=>$oInfo['pay_id']) )->getField('pay_code');
		$payModel 	= D( ucfirst($payCode), 'Payment');//D($payCode, 'Payment');
		
		//获取付款码（执行付款，或返回付款状态）
		if( isMobile() ){	//手机端访问
			return $payModel->getPayCode_Mobile( $oInfo );
		}else{
			return $payModel->getPayCode( $oInfo );
		}
	}
	
	
	
	//查询订单支付状况
	public function order_query( $oInfo ){
		$payCode 	= $this->where( array('pay_id'=>$oInfo['pay_id']) )->getField('pay_code');
		
		//vd($order_id);ee($payCode);
		//vd($oInfo);exit;
		
		$payModel 	= D( ucfirst($payCode), 'Payment');//D($payCode, 'Payment');
		
		return $payModel->order_query( $oInfo );
	}
	
	
	
	
	
	
	
}