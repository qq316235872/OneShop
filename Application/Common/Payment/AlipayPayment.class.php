<?php
namespace Common\Payment;

/**
 * 支付宝
 */
class AlipayPayment extends BasePayment{
	
	//必须部分
	//=====================================================================================================================
	//初始化
	public function _initialize(){
		$this->order_sn_name = 'out_trade_no';	//订单号名称
	}
	
	
	//------------------------------
	//返回订单号名称
	public function getOrderSn( $getValue=false ){
		
		if( $getValue && $_POST['service'] == 'alipay.wap.trade.create.direct' && $_POST['sec_id']=='0001' ){	//手机端回调需要解析加密串
			require_once(C('PAYMENT_PATH')."/lib/alipay/AlipayMobileNotify.class.php");
			
			$_SERVER['HTTP_X_WAP_PROFILE'] = 1;
			
			
			$config = $this->get_config();
			$alipay_config = $this->get_alipay_config( $config );
			
			$alipay_config['private_key_path']		= C('PAYMENT_PATH').'/lib/alipay/key/rsa_private_key.pem';	//商户的私钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
			$alipay_config['ali_public_key_path']	= C('PAYMENT_PATH').'/lib/alipay/key/alipay_public_key.pem';//支付宝公钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
			$alipay_config['sign_type']    			= '0001';
			
			$alipayNotify = new \Lib\alipay\AlipayNotify($alipay_config);
			
			return $alipayNotify->get_out_trade_no( $_POST['notify_data'] );
		}else if( $getValue ){
			return $this->getOrderSnValue();
		}else{
			return $this->order_sn_name;
		}
		
	}
	
	
	
	
	//支付方式的基本信息
	protected  function baseInfo(){
		return array(
				'pay_name'		=>	'支付宝',					//支付工具名称
				'pay_code'		=>	'alipay',					//支付英文标识
				'pay_desc'		=>	'支付宝网站(www.alipay.com) 是国内先进的网上支付平台。支付宝收款接口：在线即可开通，零预付，免年费，单笔阶梯费率，无流量限制。立即在线申请',					//支付描述
				'is_cod'		=>	0,							//货到付款
				'is_online'		=>	1,							//在线支付
				'author'		=> 	'OneShop',					//插件作者
				'version'		=>	'1.0.0',					//插件版本
				'paymentwebsite'=>	'http://www.alipay.com'		//支付方式网址
		);
	}
	
	//需要填充的配置
	public function config(){
		return array(
			array(
					'name' 		=> 'alipay_account',
					'title'		=> '支付宝帐户', 
					'remark'	=> '必填',
					'is_show'   => '1',       
					'type' 		=> 'text',   
					'value' 	=> ''
				),
			array(
					'name' 		=> 'alipay_partner',
					'title'		=> '合作者身份ID',
					'remark'	=> '必填，合作身份者id，以2088开头的16位纯数字',
					'is_show'   => '1',
					'type' 		=> 'text',
					'value' 	=> ''
			),
			array(
					'name' 		=> 'alipay_key',
					'title'		=> '交易安全校验码',
					'remark'	=> '必填，安全检验码，以数字和字母组成的32位字符',
					'is_show'   => '1',
					'type' 		=> 'text',
					'value' 	=> ''
				),
		);
	}

	/**
	 * 生成支付代码
	 * @param array $order	订单信息数组
	 * 			主要元素
	 * 				order_sn		订单号
	 * 				subject			商品名称
	 * 				order_amount	订单总额
	 * 				body			订单描述
	 * 				show_url
	 * @return multitype:string
	 */
	public function getPayCode( $order ){
	
		//获取当前支付方式的配置信息
		$config = $this->get_config();
		$alipay_config = $this->get_alipay_config( $config );
	
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" 			=> "create_direct_pay_by_user",
				"partner" 			=> trim($config['alipay_partner']),
				"payment_type"		=> 1,
				"notify_url"		=> C('NOTIFY_URL'),		//服务器异步通知页面路径,支付宝服务器主动通知商户网站里指定的页面http路径。
				"return_url"		=> C('RETURN_URL'),		//页面跳转同步通知页面路径,支付宝处理完请求后，当前页面自动跳转到商户网站里指定页面的http路径。
				"seller_email"		=> trim($config['alipay_account']),
				"out_trade_no"		=> $order['order_sn'],
				"subject"			=> $order['subject'],			//商品的标题/交易标题/订单标题/订单关键字等。该参数最长为128个汉字。
				"total_fee"			=> $order['order_amount'],		//该笔订单的资金总额，单位为RMB-Yuan。取值范围为[0.01，100000000.00]，精确到小数点后两位。
				"body"				=> $order['body'],				//对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。
				"show_url"			=> $order['show_url'],			//商品展示网址。收银台页面上，商品展示的超链接。
				"anti_phishing_key"	=> trim($config['alipay_key']),
				"exter_invoke_ip"	=> GetIP(),
				"_input_charset"	=> 'utf-8'
		);
	
		//vde($parameter);
	
		//建立请求
		require_once(C('PAYMENT_PATH')."/lib/alipay/AlipaySubmit.class.php");
		$alipaySubmit = new \Lib\alipay\AlipaySubmit($alipay_config);
		echo $alipaySubmit->buildRequestForm($parameter);
		exit;
	}
	
	
	//手机端支付
	public function getPayCode_Mobile( $order ){
		//获取当前支付方式的配置信息
		$config = $this->get_config();
		$alipay_config = $this->get_alipay_config( $config );
		
		$alipay_config['private_key_path']		= C('PAYMENT_PATH').'/lib/alipay/key/rsa_private_key.pem';	//商户的私钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
		$alipay_config['ali_public_key_path']	= C('PAYMENT_PATH').'/lib/alipay/key/alipay_public_key.pem';//支付宝公钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
		$alipay_config['sign_type']    			= '0001';													//签名方式 不需修改
		/**************************调用授权接口alipay.wap.trade.create.direct获取授权码token**************************/
		$format = "xml";			//返回格式	必填，不需要修改		
		$v 		= "2.0";			//返回格式	必填，不需要修改
		$req_id = date('Ymdhis');	//请求号		必填，须保证每次请求都是唯一
		
		//**req_data详细信息**
		$notify_url 	= C('NOTIFY_URL');					//服务器异步通知页面路径		//"http://商户网关地址/WS_WAP_PAYWAP-PHP-UTF-8/notify_url.php";
		$call_back_url 	= C('RETURN_URL');					//页面跳转同步通知页面路径	//"http://127.0.0.1:8800/WS_WAP_PAYWAP-PHP-UTF-8/call_back_url.php";
		$merchant_url 	= C('RETURN_URL');					//操作中断返回地址			//"http://127.0.0.1:8800/WS_WAP_PAYWAP-PHP-UTF-8/xxxx.php";
		$seller_email 	= trim($config['alipay_account']);	//卖家支付宝帐户
		$out_trade_no 	= $order['order_sn'];				//商户订单号
		$subject 		= $order['subject'];				//订单名称
		$total_fee 		= $order['order_amount'];			//付款金额
		
		//请求业务参数详细
		$req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';
	
		//构造要请求的参数数组，无需改动
		$para_token = array(
				"service" 	=> "alipay.wap.trade.create.direct",
				"partner" 	=> trim($alipay_config['partner']),
				"sec_id" 	=> trim($alipay_config['sign_type']),
				"format"	=> $format,
				"v"			=> $v,
				"req_id"	=> $req_id,
				"req_data"	=> $req_data,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		//建立请求
		require_once(C('PAYMENT_PATH')."/lib/alipay/AlipayMobileSubmit.class.php");
		$alipaySubmit = new \Lib\alipay\AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($para_token);
		$html_text = urldecode($html_text);
		$para_html_text = $alipaySubmit->parseResponse($html_text);		//解析远程模拟提交后返回的信息
		$request_token = $para_html_text['request_token'];				//获取request_token

		/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
		
		//业务详细
		$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" 	=> "alipay.wap.auth.authAndExecute",
				"partner" 	=> trim($alipay_config['partner']),
				"sec_id" 	=> trim($alipay_config['sign_type']),
				"format"	=> $format,
				"v"			=> $v,
				"req_id"	=> $req_id,
				"req_data"	=> $req_data,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		//建立请求
		$alipaySubmit = new \Lib\alipay\AlipaySubmit($alipay_config);
		echo $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
	}
	
	
	/**
	 * 支付通知处理
	 * @param bool $Notify 检测类型 	默认服务器通知，false则为页面跳转
	 */
	public function callback( $Notify=true ){

		if( isMobile() ){	//手机端回调
			
			require_once(C('PAYMENT_PATH')."/lib/alipay/AlipayMobileNotify.class.php");
			$config = $this->get_config();
			$alipay_config = $this->get_alipay_config( $config );
		
			$alipay_config['private_key_path']		= C('PAYMENT_PATH').'/lib/alipay/key/rsa_private_key.pem';	//商户的私钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
			$alipay_config['ali_public_key_path']	= C('PAYMENT_PATH').'/lib/alipay/key/alipay_public_key.pem';//支付宝公钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
			$alipay_config['sign_type']    			= '0001';													//签名方式 不需修改
		}else{				//PC端回调
			require_once(C('PAYMENT_PATH')."/lib/alipay/AlipayNotify.class.php");
			$config = $this->get_config();
			$alipay_config = $this->get_alipay_config( $config );
		}
		
		//
		$alipayNotify = new \Lib\alipay\AlipayNotify($alipay_config);
		
		
		//验证是否为支付宝发送过来的请求
		if( $Notify ){		//服务器通知
			
			sf(1,"C:\\paylog\\Notify.php");
			
			$verify_result = $alipayNotify->verifyNotify();			//	$_POST
		}else{				//页面跳转
			$verify_result = $alipayNotify->verifyReturn();			//	$_GET
		}
		
		if( $verify_result ){
			if( $Notify ){	//服务器通知，获取支付状态
				/*
				$doc = new \DOMDocument();
				if ($alipay_config['sign_type'] == 'MD5') {
					$doc->loadXML($_POST['notify_data']);
				}
				if ($alipay_config['sign_type'] == '0001') {
					$doc->loadXML($alipayNotify->decrypt($_POST['notify_data']));
				}
				if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
					//商户订单号
					$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
					//支付宝交易号
					$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
					//交易状态
					$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
				}else{
					return false;
				}
				*/
				$trade_status = $_POST['trade_status'];
			}else{	//页面跳转通知
				$trade_status = $_GET['result'];
			}
			
			if( in_array($trade_status,array('TRADE_FINISHED','TRADE_SUCCESS','success') ) ){
				return true;
			}
		}
		return false;
	}

	
	
	
	//支付方式扩展
	//=====================================================================================================================
	public function get_alipay_config( $config ){
		$alipay_config['partner']		= $config['alipay_partner'];//合作身份者id，以2088开头的16位纯数字
		$alipay_config['key']			= $config['alipay_key'];	//安全检验码，以数字和字母组成的32位字符
		$alipay_config['sign_type']    	= strtoupper('MD5');
		$alipay_config['input_charset']	= strtolower('utf-8');
		$alipay_config['cacert']    	= C('PAYMENT_PATH').'/lib/alipay/cacert.pem';	//ca证书路径地址，用于curl中ssl校验//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['transport']    	= 'http';					//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http

		return $alipay_config;
	}
	

	
	
	
	
	
	
	
}
