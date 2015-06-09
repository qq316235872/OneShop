<?php
namespace Admin\Controller;

/**
 * 支付方式
 */
class PaymentController extends AdminController {

        Public $PaymentModel;
        Public function _initialize() {
            parent::_initialize();
            $this->PaymentModel=D('Common/Payment');
        }
    
    
	//支付方式列表
	public function index(){
            //查询数据库中已经安装的支付组建
            $installedPayment = $this->lists($this->PaymentModel,array(),'pay_order desc');
            //所有可供调用的支付组件
            $payment_list = $this->PaymentModel->combination( $installedPayment );
            
            $this->assign('payment_list',$payment_list);
            $this->display();
	}
		
	//编辑支付
	public function edit(){
		$Payment = D('Payment');
		if( I('get.pay_code') ){
			$info = $Payment->getInfo( array('pay_code'=>I('get.pay_code')) );
			
			//vd($info);
			
			$this->assign('info',$info);	
		}else if( IS_POST ){
			$this->jumpUrl 	= U('index');			
			$dbField 		= $Payment->getDbFields();	//数据表中的字段
			$config 		= array();
			foreach( $_POST as $key => $value ){
				if( !in_array($key, $dbField) ){
					$config[ $key ] = $value;
				}
			}
			$_POST['pay_config'] = serialize($config);
			$this->do_edit($Payment,array('安装','编辑'));
		}
		$this->display();
	}
	
	//删除
	public function del(){
		$this->do_del( D('Payment'),array('pay_code'=>I('get.pay_code')) );
	}

}
