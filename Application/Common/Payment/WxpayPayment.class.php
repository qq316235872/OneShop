<?php
namespace Common\Payment;

/**
 * 支付宝
 */
class WxpayPayment extends BasePayment{
	
	
	//必须部分
	//=====================================================================================================================
	//初始化
	public function _initialize(){
		$this->order_sn_name = 'out_trade_no';	//订单号名称
	}
	
	//返回订单号
	public function getOrderSnValue(){
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		//使用通用通知接口
		$LIB_PATH = C('PAYMENT_PATH').'/lib/wxpay';
		require_once($LIB_PATH.'/WxPayPubHelper.php');
		$notify = new \Lib\wxpay\Notify_pub();
		$retArr = $notify->xmlToArray($xml);
		return $retArr[$this->order_sn_name];
	}
	
	
	//支付方式的基本信息
	protected  function baseInfo(){
		return array(
				'pay_name'		=>	'微信支付',					//支付工具名称
				'pay_code'		=>	'wxpay',					//支付英文标识
				'pay_desc'		=>	'微信支付(mp.weixin.qq.com) 是腾讯公司开发的网上支付平台。',					//支付描述
				'is_cod'		=>	0,							//货到付款
				'is_online'		=>	1,							//在线支付
				'author'		=> 	'OneShop',					//插件作者
				'version'		=>	'1.0.0',					//插件版本
				'paymentwebsite'=>	'http://mp.weixin.qq.com'		//支付方式网址
		);
	}
	
	//需要填充的配置
	public function config(){
		return array(
			array(
					'name' 		=> 'APPID',
					'title'		=> 'APPID', 
					'remark'	=> '必填，微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看',
					'is_show'   => '1',       
					'type' 		=> 'text',   
					'value' 	=> ''
			),
			array(
					'name' 		=> 'APPSECRET',
					'title'		=> 'APPSECRET',
					'remark'	=> '必填，审核后在公众平台开启开发模式后可查看',
					'is_show'   => '1',
					'type' 		=> 'text',
					'value' 	=> ''
			),
			array(
					'name' 		=> 'partnerid',
					'title'		=> 'partnerid',
					'remark'	=> '必填，受理商ID（商户号），身份标识。',
					'is_show'   => '1',
					'type' 		=> 'text',
					'value' 	=> ''
			),
			array(
					'name' 		=> 'partnerkey',
					'title'		=> 'partnerkey',
					'remark'	=> '必填，商户支付密钥Key。审核通过后，在微信发送的邮件中查看',
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
		
		//vde($order);
		
		//建立请求
		$LIB_PATH = C('PAYMENT_PATH').'/lib/wxpay';
		require_once($LIB_PATH.'/WxPayPubHelper.php');
		
		//使用统一支付接口
		$unifiedOrder = new \Lib\wxpay\UnifiedOrder_pub();
		
		//设置统一支付接口参数
		//$this->setParameter( $unifiedOrder );

		//sign已填,商户无需重复填写
		$unifiedOrder->setParameter("body",$order['subject']);					//商品描述
		//自定义订单号，此处仅作举例
		$CONFIG = $this->get_config();
		$unifiedOrder->setConfig( $CONFIG );
		//$unifiedOrder->setParameter("appid",$CONFIG['APPID']);
		//$unifiedOrder->setParameter("mch_id",$CONFIG['partnerid']);
		
		$timeStamp = time();
		$unifiedOrder->setParameter("out_trade_no",$order['order_sn']);			//商户订单号
		$unifiedOrder->setParameter("total_fee",$order['order_amount']*100);	//总金额
		$unifiedOrder->setParameter("notify_url",C('NOTIFY_URL'));				//通知地址
		$unifiedOrder->setParameter("trade_type","NATIVE");						//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号
		//$unifiedOrder->setParameter("attach","XXXX");//附加数据
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
		
		//获取统一支付接口结果
		$unifiedOrderResult = $unifiedOrder->getResult();
		
		//商户根据实际情况设置相应的处理流程
		if ($unifiedOrderResult["return_code"] == "FAIL")
		{
			//商户自行增加处理流程
			echo "通信出错：".$unifiedOrderResult['return_msg']."<br>";
		}
		elseif($unifiedOrderResult["result_code"] == "FAIL")
		{
			//商户自行增加处理流程
			echo "错误代码：".$unifiedOrderResult['err_code']."<br>";
			echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
		}
		elseif($unifiedOrderResult["code_url"] != NULL)
		{
			//从统一支付接口获取到code_url
			$code_url = $unifiedOrderResult["code_url"];
			//商户自行增加处理流程
			//......
		}
		/**/
		
		//模版输出
		require_once($LIB_PATH.'/tmp.php');
		exit;
	}
	
	//获取配置信息
	public function get_config(){
		$config = parent::get_config();
		$config['curl_timeout'] = 30;
		return $config;
	}
	
	//设置配置信息
	private function setParameter( $obj ){
		//获取当前支付方式的配置信息
		$CONFIG = $this->get_config();
	
		//vd($CONFIG);
	
		//数据库配置信息
		$obj->setParameter("appid",$CONFIG['APPID']);			//
		//$obj->setParameter("secret",$CONFIG['APPSECRET']);		//
		$obj->setParameter("mch_id",$CONFIG['partnerid']);		//
		//$obj->setParameter("partnerkey",$CONFIG['partnerkey']);	//
		$obj->curl_timeout = 30;								//
	}
	
	//手机端支付
	public function getPayCode_Mobile( $order ){
		
		$LIB_PATH = C('PAYMENT_PATH').'/lib/wxpay';
		require_once($LIB_PATH.'/WxPayPubHelper.php');
		
		//使用jsapi接口
		$jsApi = new \Lib\wxpay\JsApi_pub();
		
		
		$CONFIG = $this->get_config();
		$jsApi->setConfig( $CONFIG );
		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		if (!isset($_GET['code']))
		{
			//触发微信返回code码
			$url = $jsApi->createOauthUrlForCode('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);
			Header("Location: $url");
		}else
		{
			//获取code码，以获取openid
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
		}
		
		//echo $openid."<br/>";exit;
		
		//=========步骤2：使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new \Lib\wxpay\UnifiedOrder_pub();
		$unifiedOrder->setConfig( $CONFIG );
		//设置统一支付接口参数
		//设置必填参数
		$unifiedOrder->setParameter("openid","$openid");						//
		$unifiedOrder->setParameter("body",$order['subject']);					//商品描述
		//自定义订单号，此处仅作举例
		$unifiedOrder->setParameter("out_trade_no",$order['order_sn']);			//商户订单号
		$unifiedOrder->setParameter("total_fee",$order['order_amount']*100);	//总金额
		
		$unifiedOrder->setParameter("notify_url", C('NOTIFY_URL') );			//通知地址
		$unifiedOrder->setParameter("trade_type","JSAPI");						//交易类型
		
		$prepay_id = $unifiedOrder->getPrepayId();
		
		//echo "prepay_id::".$prepay_id;//exit;
		
		
		//=========步骤3：使用jsapi调起支付============
		//echo 'prepay_id:'.$prepay_id."<br/>";exit;
		$jsApi->setPrepayId($prepay_id);
		
		$jsApiParameters = $jsApi->getParameters();
		
		
		//echo $jsApiParameters;//exit;
		
		
		//模版输出
		require_once($LIB_PATH.'/tmp_mobile.php');
		exit;
	}
	
	
	
	
	
	/**
	 * 支付通知处理
	 * @param bool $Notify 检测类型 	默认服务器通知，false则为页面跳转
	 */
	public function callback( $Notify=true ){

		//建立请求
		$LIB_PATH = C('PAYMENT_PATH').'/lib/wxpay';
		require_once($LIB_PATH.'/log_.php');
		require_once($LIB_PATH.'/WxPayPubHelper.php');
		
		//使用通用通知接口
		$notify = new \Lib\wxpay\Notify_pub();
		
		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify->saveData($xml);
		
		
		
		$CONFIG = $this->get_config();
		$notify->setConfig( $CONFIG );
		
		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;
		
		//sf($notify->data,"C:\\paylog\\notify_data.php");
		
		//订单状态
		if( $notify->checkSign()==TRUE && $notify->data["return_code"]=="SUCCESS" && $notify->data["return_code"]=="SUCCESS" ){
			return true;
		}
		
		return false;
	}
	//=====================================================================================================================
	//（微信服务器）订单查询，获取返回的所有信息
	public function order_query($out_trade_no){
		
		//建立请求
		$LIB_PATH = C('PAYMENT_PATH').'/lib/wxpay';
		require_once($LIB_PATH.'/WxPayPubHelper.php');
		
		//使用订单查询接口
		$orderQuery = new \Lib\wxpay\OrderQuery_pub();

		//设置必填参数
		$orderQuery->setConfig( $this->get_config() );
		$orderQuery->setParameter("out_trade_no","$out_trade_no");//商户订单号
		 
		//获取订单查询结果
		$orderQueryResult = $orderQuery->getResult();

		return $orderQueryResult;
	}
	
	//订单是否成功支付
	public function is_paid($out_trade_no){
		$orderQueryResult = $this->order_query($out_trade_no);
		if($orderQueryResult['trade_state']=='SUCCESS'){
			return 1;
		}
		return 0;
	}
	
	//打印订单详情，测试用
	public function print_order_query($out_trade_no){
		
		$orderQueryResult = $this->order_query($out_trade_no);
		
		//商户根据实际情况设置相应的处理流程,此处仅作举例
		if ($orderQueryResult["return_code"] == "FAIL") {
			echo "通信出错：".$orderQueryResult['return_msg']."<br>";
		}
		elseif($orderQueryResult["result_code"] == "FAIL"){
			echo "错误代码：".$orderQueryResult['err_code']."<br>";
			echo "错误代码描述：".$orderQueryResult['err_code_des']."<br>";
		}
		else{
			echo "交易状态：".$orderQueryResult['trade_state']."<br>";
			echo "设备号：".$orderQueryResult['device_info']."<br>";
			echo "用户标识：".$orderQueryResult['openid']."<br>";
			echo "是否关注公众账号：".$orderQueryResult['is_subscribe']."<br>";
			echo "交易类型：".$orderQueryResult['trade_type']."<br>";
			echo "付款银行：".$orderQueryResult['bank_type']."<br>";
			echo "总金额：".$orderQueryResult['total_fee']."<br>";
			echo "现金券金额：".$orderQueryResult['coupon_fee']."<br>";
			echo "货币种类：".$orderQueryResult['fee_type']."<br>";
			echo "微信支付订单号：".$orderQueryResult['transaction_id']."<br>";
			echo "商户订单号：".$orderQueryResult['out_trade_no']."<br>";
			echo "商家数据包：".$orderQueryResult['attach']."<br>";
			echo "支付完成时间：".$orderQueryResult['time_end']."<br>";
		}
	}
	
	
	
	
	
	
	
	
	//支付方式扩展
	//=====================================================================================================================

	
	
	
	
	
	
}
