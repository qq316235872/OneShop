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
            $installedPayment = $this->lists($this->PaymentModel,array());
            //所有可供调用的支付组件
            $payment_list = $this->PaymentModel->combination( $installedPayment );
            $this->assign('payment_list',list_sort_by($payment_list,'pay_order','desc'));
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
                        $Pk = $Payment->getPk();
                        if($Payment->create()){
                                if( $_POST[$Pk] ){
                                    if($Payment->save()){
                                        $this->success('编辑成功',U('index'));
                                    }else{
                                        $this->error('编辑失败');
                                    }

                                }else{
                                    if($Payment->add()){
                                        $this->success('安装成功',U('index'));
                                    }else{
                                        $this->error('安装失败');
                                    }

                                }
                        }else{
                            $this->error( $Payment->getError() );
                        }

		}
		$this->display();
	}
	
	//卸载
	public function del(){
            if(D('Payment')->where(array('pay_code'=>I('get.pay_code')))->delete()){
                $this->success("卸载成功",U('index'));
            }else{
                $this->error('卸载失败');
            }
	}

}
