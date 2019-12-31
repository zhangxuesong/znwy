define(['core','tpl'], function(core, tpl) {
	var modal = {
		iswxapp:0
	};	
	modal.init = function(params) {		
		wx.miniProgram.getEnv(function(res) {
			if(res.miniprogram){
				modal.iswxapp=1;
			};
		});
		
		$("#exchange").click(function () {
			var ischecked=$('#exchange').is(':checked');
			if(ischecked){
				$('#btn-pay').html('兑换');
			}
			else{
				$('#btn-pay').html('支付');
			}
		});

		$("#btn-pay").click(function () {
			var money = $.trim( $("#money").val());
			if (isNaN(money) || money<=0){
				RhUI.toast.show('输入金额有误!');
				return;
			}		
		   
			var bid = $.trim( $("#bid").val());		
			var exchange = 1;
			var ischecked=$('#exchange').is(':checked');
			if(ischecked) exchange = 0;
	
			if(exchange == 1){
				RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
					 core.json('business/pay', {id:bid,money:money,exchange:exchange,iswxapp:modal.iswxapp,payfrom:1}, function(rjson) {
							if (rjson.status != 1) {
								$('#btn-pay').removeAttr('submit');							
								RhUI.toast.show(rjson.result.message);
								return
							}
							var wechat = rjson.result.wechat;	
							if(modal.iswxapp==1){
								var path = '/pages/mypay/pay?params='+wechat;
								wx.miniProgram.navigateTo({
									url: path
								});
								return
							}				
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
										location.href = wechat.returl;
										return
									} 
									else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
										$('.btn-pay').removeAttr('submit');
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
				});
			}
			else {
				RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
					core.json('business/pay', {id:bid,money:money,exchange:exchange}, function(rjson) {
						if (rjson.status != 1) {
							$('#btn-pay').removeAttr('submit');
							RhUI.toast.show(rjson.result.message);
							return
						}
						RhUI.toast.show('兑换成功，请等待审核.');
						location.href = core.getUrl('member');
					});
				});
			}
		});
	};
	modal.initalipay = function(params) {					
		$("#exchange").click(function () {
			var ischecked=$('#exchange').is(':checked');
			if(ischecked){
				$('#btn-pay').html('兑换');
			}
			else{
				$('#btn-pay').html('支付');
			}
		});

		$("#btn-pay").click(function () {
			var money = $.trim( $("#money").val());
			if (isNaN(money) || money<=0){
				RhUI.toast.show('输入金额有误!');
				return;
			}		
		   
			var bid = $.trim( $("#bid").val());		
			var exchange = 1;
			var ischecked=$('#exchange').is(':checked');
			if(ischecked) exchange = 0;
	
			if(exchange == 1){
				RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
					 core.json('business/pay', {id:bid,money:money,exchange:exchange,iswxapp:0,payfrom:2}, function(rjson) {
							if (rjson.status != 1) {
								$('#btn-pay').removeAttr('submit');							
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
				});
			}
			else {
				RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
					core.json('business/pay', {id:bid,money:money,exchange:exchange}, function(rjson) {
						if (rjson.status != 1) {
							$('#btn-pay').removeAttr('submit');
							RhUI.toast.show(rjson.result.message);
							return
						}
						RhUI.toast.show('兑换成功，请等待审核.');
						location.href = core.getUrl('member');
					});
				});
			}
		});
	};
	return modal
});