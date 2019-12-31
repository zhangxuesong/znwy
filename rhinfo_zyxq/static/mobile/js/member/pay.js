define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		params: {}
	};
	modal.init = function(params) {		
		$('#weixin-btn').unbind('click').click(function() {	
			//	$('#wx_payform').submit();
			if ($('#weixin-btn').attr('stop')) {
				return
			}
			$('#weixin-btn').attr('stop', 1);
			if($('#payparams').isEmpty()){				
				return
			}
			modal.payWechat($('#payparams').val());
		});		

		$('#alipay-btn').unbind('click').click(function() {				
			$('#al_payform').submit();			
		});	
		$('#aliapp-btn').unbind('click').click(function() {				
			if ($('#aliapp-btn').attr('stop')) {
				return
			}
			$('#aliapp-btn').attr('stop', 1);
			if($('#payparams').isEmpty()){				
				return
			}
			modal.payAlipay($('#payparams').val());
		});	
		$('#wxapp-btn').unbind('click').click(function() {
			var path = '/pages/pay/pay?params='+$("#wxapp_params").val();
			wx.miniProgram.navigateTo({
				url: path
			}); 
		});	
	}		
	modal.payWechat = function(params) {
		core.json('mycash/wechat', {'params':params}, function(rjson) {			
			if (rjson.status != 1) {
				$('#weixin-btn').removeAttr('stop');							
				RhUI.toast.show(rjson.result.message);
				return
			}
			
			var wechat = rjson.result.wechat;
			
			if(wechat.iswap==1){
				location.href = wechat.mweb_url;
				return
			}
			if(wechat.isymf==1){
				location.href = wechat.ymfurl;
				return
			}			
			if(wechat.iswepay==1){
				location.href = core.getUrl('wepay')+'&params=' + wechat.params;
				return
			}
			function onBridgeReady() {
				WeixinJSBridge.invoke('getBrandWCPayRequest', {
					'appId':  wechat.appId,
					'timeStamp': wechat.timeStamp,
					'nonceStr': wechat.nonceStr,
					'package': wechat.package,
					'signType': wechat.signType,
					'paySign': wechat.paySign
				}, function(res) {
					if (res.err_msg == 'get_brand_wcpay_request:ok') {								
						location.href = wechat.successurl;
						return
					} 
					else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
						$('#weixin-btn').removeAttr('stop');
						RhUI.toast.show('取消支付')
					} 
				})
			}
			if  (typeof WeixinJSBridge  ==  "undefined") {
				if ( document.addEventListener ) {
					document.addEventListener('WeixinJSBridgeReady',  onBridgeReady,  false)
				} 
				else if (document.attachEvent) {
					document.attachEvent('WeixinJSBridgeReady',  onBridgeReady);
					document.attachEvent('onWeixinJSBridgeReady',  onBridgeReady)
				}
			}
			else {
				onBridgeReady()
			}				
		}, false, true);
	};
	modal.payAlipay = function(params) {
		core.json('mycash/alipay', {'params':params}, function(rjson) {			
			if (rjson.status != 1) {
				$('#aliapp-btn').removeAttr('stop');							
				RhUI.toast.show(rjson.result.message);
				return
			}			
			var alipay = rjson.result.alipay;	
			function alipay_ready(callback) {
			  // 如果jsbridge已经注入则直接调用
			  if (window.AlipayJSBridge) {
				callback && callback();
			  } 
			  else {
				// 如果没有注入则监听注入的事件
				document.addEventListener('AlipayJSBridgeReady', callback, false);
			  }
			}
			alipay_ready(function () {
			   AlipayJSBridge.call("tradePay", {
					tradeNO: alipay.trade_no, // 必传，此使用方式下该字段必传				
				}, function(result) {
					if(result.resultCode==9000){
						location.href = alipay.returl;
						return
					}
					else if(result.resultCode==6001){
						RhUI.toast.show('取消支付');
					}
					else{
						RhUI.toast.show('支付失败');
					}
				});
			});           
			
		}, false, true);
	};
	return modal
});