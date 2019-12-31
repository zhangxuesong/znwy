define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		bid:0,
		minimumcharge: 0,
		iswxapp:0,
		payfrom:1
	};
	modal.init = function(params) {	
		modal.minimumcharge = params.minimumcharge;
		modal.bid = params.bid;
		modal.payfrom = params.payfrom;
		$('#money').bind('input propertychange', function() {			
			$('#btn-next').addClass('disabled'); 
			if ($(this).isNumber() && !$(this).isEmpty() && parseFloat($(this).val()) > 0) {
				$('#btn-next').removeClass('disabled')
			}
		});
		
		wx.miniProgram.getEnv(function(res) {
			if(res.miniprogram){
				modal.iswxapp=1;
			};
		});	
		
		$('#btn-next').click(function() {
			var money = $.trim($('#money').val());
			var showpay = false;			
			if (!$.isEmpty(money)) {
				if ($.isNumber(money) && parseFloat(money) > 0) {
					if (modal.minimumcharge > 0) {
						if (parseFloat(money) < modal.minimumcharge) {
							RhUI.toast.show('最低充值金额为' + modal.minimumcharge + '元!');
							return
						} else {
							showpay = true
						}
					} else {
						showpay = true
					}
				}
			}
			if (!showpay) {
				return
			}
			$('#btn-next').hide();
			if (modal.payfrom==1) {
				$('#weixin-btn').show()
			}
			if (modal.payfrom==2) {
				$('#alipay-btn').show()
			}
		});
		$(document).on('click', '#weixin-btn', function() {
			if ($('.btn-pay').attr('submit')) {
				return
			}
			var money = $('#money').val();
			if (money <= 0) {
				RhUI.toast.show('充值金额必须大于0!');
				return
			}
			if (!$('#money').isNumber()) {
				RhUI.toast.show('请输入数字金额!');
				return
			}
			$('.btn-pay').attr('submit', 1);
			core.json('business/recharge', {
				id:modal.bid,
				money: money,
				iswxapp:modal.iswxapp,
				payfrom:1
			}, function(rjson) {
				if (rjson.status != 1) {
					$('.btn-pay').removeAttr('submit');
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
			}, false, true)
		});	
		$(document).on('click', '#alipay-btn', function() {
			if ($('.btn-pay').attr('submit')) {
				return
			}
			var money = $('#money').val();
			if (money <= 0) {
				RhUI.toast.show('充值金额必须大于0!');
				return
			}
			if (!$('#money').isNumber()) {
				RhUI.toast.show('请输入数字金额!');
				return
			}
			$('.btn-pay').attr('submit', 1);
			core.json('business/recharge', {
				id:modal.bid,
				money: money,
				iswxapp:0,
				payfrom:2
			}, function(rjson) {
				if (rjson.status != 1) {
					$('.btn-pay').removeAttr('submit');
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
			}, false, true)
		});	
	};	
	return modal
});