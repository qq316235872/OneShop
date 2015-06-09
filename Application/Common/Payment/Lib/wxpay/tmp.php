
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>微信安全支付</title>
</head>
<body>
	<div align="center" id="qrcode">
	</div>
	<div align="center">
		<p>订单号：<?php echo $order['order_sn']; ?></p>
	</div>
	<!--  
	<div align="center">
		<form  action="./order_query.php" method="post">
			<input name="out_trade_no" type='hidden' value="<?php echo $order['order_sn']; ?>">
		    <button type="submit" >查询订单状态</button>
		</form>
	</div>
	<br>
	<div align="center">
		<form  action="./refund.php" method="post">
			<input name="out_trade_no" type='hidden' value="<?php echo $order['order_sn']; ?>">
			<input name="refund_fee" type='hidden' value="1">
		    <button type="submit" >申请退款</button>
		</form>
	</div>
	<br>
	-->
	<div align="center">
		<a href="javascript:history.go(-1);">返回</a>
	</div>
</body>
	<script src="/<?php echo $LIB_PATH; ?>/jquery-2.0.3.min.js"></script>
	<script src="/<?php echo $LIB_PATH; ?>/qrcode.js"></script>
	<script>
		if(<?php echo $unifiedOrderResult["code_url"] != NULL; ?>)
		{
			var url = "<?php echo $code_url;?>";
			//参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
			var qr = qrcode(10, 'M');
			qr.addData(url);
			qr.make();
			var wording=document.createElement('p');
			wording.innerHTML = "扫我，扫我";
			var code=document.createElement('DIV');
			code.innerHTML = qr.createImgTag();
			var element=document.getElementById("qrcode");
			element.appendChild(wording);
			element.appendChild(code);
		}
		var timer;
		var jump=1;
		$(function(){
			//轮询订单付款状态
			timer = window.setInterval(check_order,1000); 
			function check_order(){
				$.get("/Order/is_paid", { order_sn: "<?php echo $order['order_sn']; ?>" },function(data){
					console.log(data+'--'+jump);
					if(data*1==1 && jump*1==1){	//支付成功后页面跳转
						window.location.href="/Order/callback/order_sn/<?php echo $order['order_sn']; ?>"; 
						jump=0;
						clearInterval(timer);
					}
				});
			}
		})

		
	</script>
</html>