define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		minimumcharge: 0,
		pid: 0,
		rid: 0,
		sid: 0,
		compid: 0,
		expid: 0,
		iswxapp:0,
		payfrom:1
	};
	modal.init = function(params) {	
		modal.minimumcharge = params.minimumcharge;
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
							RhUI.toast.show('最低充值金额为' + modal.minimumcharge + '元');
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
			var money = $.trim($('#money').val());
			if (money <= 0) {
				RhUI.toast.show('充值金额必须大于0');
				return
			}
			if (!$('#money').isNumber()) {
				RhUI.toast.show('请输入数字金额');
				return
			}
			if (modal.minimumcharge > 0) {
				if (parseFloat(money) < modal.minimumcharge) {
					RhUI.toast.show('最低充值金额为' + modal.minimumcharge + '元');
					return
				} 
			} 
			$('.btn-pay').attr('submit', 1);
			core.json('service/recharge', {
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
                console.log('xxxxxxxxx',wechat);
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
			var money = $.trim($('#money').val());
			if (money <= 0) {
				RhUI.toast.show('充值金额必须大于0');
				return
			}
			if (!$('#money').isNumber()) {
				RhUI.toast.show('请输入数字金额');
				return
			}
			if (modal.minimumcharge > 0) {
				if (parseFloat(money) < modal.minimumcharge) {
					RhUI.toast.show('最低充值金额为' + modal.minimumcharge + '元');
					return
				} 
			} 
			$('.btn-pay').attr('submit', 1);
			core.json('service/recharge', {
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
	modal.initGive = function(params) {	
		modal.minimumcharge = params.minimumcredit;		
		$('#credit').bind('input propertychange', function() {			
			$('#btn-next').addClass('disabled');
			if ($(this).isNumber() && !$(this).isEmpty() && parseFloat($(this).val()) > 0) {
				$('#btn-next').removeClass('disabled');
			}
		});
		$('#btn-next').click(function() {
			var credit= $.trim($('#credit').val());				
			var mycredit= $.trim($('#mycredit').val());				
			if (!$.isEmpty(credit)) {
				credit = parseFloat(credit);
				mycredit = parseFloat(mycredit);
				if(mycredit >= credit){
					if ($.isNumber(credit) && credit > 0) {
						if (modal.minimumcharge > 0) {
							if (credit < modal.minimumcharge) {
								RhUI.toast.show('最低赠送' + modal.minimumcharge);
								return
							} 
						} 
					}
					else{
						RhUI.toast.show('赠送数量不能为空');
						return
					}
				}
				else{
					RhUI.toast.show('赠送数量不能大于当前数量');
					return
				}
			}
			else{
				RhUI.toast.show('赠送数量不能为空');
				return
			}
			var mobile= $.trim($('#mobile').val());	
			if ($.isEmpty(mobile)) {
				RhUI.toast.show('手机号不能为空');
				return
			}
			else{
				core.json('service/checkmobile',{
				mobile: mobile
				}, function(ret) {
					if (ret.status != 1) {						
						RhUI.toast.show(ret.result.message);
						return
					}
					$("#myform").submit();						
				}, false, true);
			}			
		});		
	};
	modal.initSubmit = function(params) {	
		modal.minimumcharge = params.minimumcredit;				
		$('#btn-next').click(function() {
			var credit= $.trim($('#credit').val());				
			var mycredit= $.trim($('#mycredit').val());				
			if (!$.isEmpty(credit)) {
				credit = parseFloat(credit);
				mycredit = parseFloat(mycredit);
				if(mycredit >= credit){
					if ($.isNumber(credit) && credit > 0) {
						if (modal.minimumcharge > 0) {
							if (credit < modal.minimumcharge) {
								RhUI.toast.show('最低赠送' + modal.minimumcharge);
								return
							} 
						} 
					}
					else{
						RhUI.toast.show('赠送数量不能为空');
						return
					}
				}
				else{
					RhUI.toast.show('赠送数量不能大于当前数量');
					return
				}
			}
			else{
				RhUI.toast.show('赠送数量不能为空');
				return
			}
			var uid = $.trim($('#uid').val());			
			core.json('service/givesubmit',{
				credit:credit,
				uid:uid
				}, function(ret) {
					if (ret.status != 1) {						
						RhUI.toast.show(ret.result.message);
						return
					}
					RhUI.alert(ret.result.message, '', function() {					
						location.href = core.getUrl('member/index');					
					})
				}, false, true);
		});		
	};
	modal.initSms = function(params) {	
		modal.minimumcharge = params.minimumcharge;
		modal.payfrom = params.payfrom;
		modal.rid = params.rid;
		$('#smsqty').bind('input propertychange', function() {			
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
			var smsqty = $.trim($('#smsqty').val());
			var showpay = false;			
			if (!$.isEmpty(smsqty)) {
				if ($.isNumber(smsqty) && parseFloat(smsqty) > 0) {
					if (modal.minimumcharge > 0) {
						if (parseFloat(smsqty) < modal.minimumcharge) {
							RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
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
			$('#weixin-btn').show()
			$('#alipay-btn').show()
		});
		$(document).on('click', '#weixin-btn', function() {
			if ($('.btn-pay').attr('submit')) {
				return
			}
			var smsqty = $('#smsqty').val();
			if (smsqty <= 0) {
				RhUI.toast.show('充值短信条数必须大于0');
				return
			}
			if (!$('#smsqty').isNumber()) {
				RhUI.toast.show('请输入数字');
				return
			}
			if (modal.minimumcharge > 0) {
				if (parseFloat(smsqty) < modal.minimumcharge) {
					RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
					return
				}
			}
			$('.btn-pay').attr('submit', 1);
			core.json('service/smsrecharge', {
				smsqty:smsqty,rid:modal.rid,iswxapp:modal.iswxapp,payfrom:1
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
			var smsqty = $('#smsqty').val();
			if (smsqty <= 0) {
				RhUI.toast.show('充值短信条数必须大于0');
				return
			}
			if (!$('#smsqty').isNumber()) {
				RhUI.toast.show('请输入数字');
				return
			}
			if (modal.minimumcharge > 0) {
				if (parseFloat(smsqty) < modal.minimumcharge) {
					RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
					return
				}
			}
			$('.btn-pay').attr('submit', 1);
			core.json('service/smsrecharge', {
				smsqty:smsqty,rid:modal.rid,iswxapp:0,payfrom:2
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
	modal.initSmspro = function(params) {			
		modal.pid = params.pid;				
		modal.payfrom = params.payfrom;
		wx.miniProgram.getEnv(function(res) {			
			if(res.miniprogram){
				modal.iswxapp=1;				
			};
		});	

		$(document).on('click', '#weixin-btn', function() {
			if ($('.btn-pay').attr('submit')) {
				return
			}
			var yearprice = $('#yearprice').val();
			if (yearprice <= 0) {
				RhUI.toast.show('未定义价格');
				return
			}

			$('.btn-pay').attr('submit', 1);
			core.json('service/prorecharge', {
				yearprice:yearprice,pid:modal.pid,iswxapp:modal.iswxapp,payfrom:1
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
			var yearprice = $('#yearprice').val();
			if (yearprice <= 0) {
				RhUI.toast.show('未定义价格');
				return
			}

			$('.btn-pay').attr('submit', 1);
			core.json('service/prorecharge', {
				yearprice:yearprice,pid:modal.pid,iswxapp:0,payfrom:2
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
	modal.initSmsstore = function(params) {	
		modal.minimumcharge = params.minimumcharge;
		modal.payfrom = params.payfrom;
		modal.sid = params.sid;
		$('#smsqty').bind('input propertychange', function() {			
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
			var smsqty = $.trim($('#smsqty').val());
			var showpay = false;			
			if (!$.isEmpty(smsqty)) {
				if ($.isNumber(smsqty) && parseFloat(smsqty) > 0) {
					if (modal.minimumcharge > 0) {
						if (parseFloat(smsqty) < modal.minimumcharge) {
							RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
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
			$('#weixin-btn').show()
			$('#alipay-btn').show()
		});
		$(document).on('click', '#weixin-btn', function() {
			if ($('.btn-pay').attr('submit')) {
				return
			}
			var smsqty = $('#smsqty').val();
			if (smsqty <= 0) {
				RhUI.toast.show('充值短信条数必须大于0');
				return
			}
			if (!$('#smsqty').isNumber()) {
				RhUI.toast.show('请输入数字');
				return
			}
			if (modal.minimumcharge > 0) {
				if (parseFloat(smsqty) < modal.minimumcharge) {
					RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
					return
				}
			}
			$('.btn-pay').attr('submit', 1);
			core.json('service/smsstore', {
				smsqty:smsqty,sid:modal.sid,iswxapp:modal.iswxapp,payfrom:1
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
			var smsqty = $('#smsqty').val();
			if (smsqty <= 0) {
				RhUI.toast.show('充值短信条数必须大于0');
				return
			}
			if (!$('#smsqty').isNumber()) {
				RhUI.toast.show('请输入数字');
				return
			}
			if (modal.minimumcharge > 0) {
				if (parseFloat(smsqty) < modal.minimumcharge) {
					RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
					return
				}
			}
			$('.btn-pay').attr('submit', 1);
			core.json('service/smsstore', {
				smsqty:smsqty,sid:modal.sid,iswxapp:0,payfrom:2
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
	modal.initSmsexpress = function(params) {	
		modal.minimumcharge = params.minimumcharge;
		modal.expid = params.expid;
		modal.compid = params.compid;
		modal.payfrom = params.payfrom;
		$('#smsqty').bind('input propertychange', function() {			
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
			var smsqty = $.trim($('#smsqty').val());
			var showpay = false;			
			if (!$.isEmpty(smsqty)) {
				if ($.isNumber(smsqty) && parseFloat(smsqty) > 0) {
					if (modal.minimumcharge > 0) {
						if (parseFloat(smsqty) < modal.minimumcharge) {
							RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
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
			$('#weixin-btn').show()
			$('#alipay-btn').show()
		});
		$(document).on('click', '#weixin-btn', function() {
			if ($('.btn-pay').attr('submit')) {
				return
			}
			var smsqty = $('#smsqty').val();
			if (smsqty <= 0) {
				RhUI.toast.show('充值短信条数必须大于0');
				return
			}
			if (!$('#smsqty').isNumber()) {
				RhUI.toast.show('请输入数字');
				return
			}
			if (modal.minimumcharge > 0) {
				if (parseFloat(smsqty) < modal.minimumcharge) {
					RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
					return
				}
			}
			$('.btn-pay').attr('submit', 1);
			core.json('service/smsexpress', {
				smsqty:smsqty,compid:modal.compid,expid:modal.expid,iswxapp:modal.iswxapp,payfrom:1
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
			var smsqty = $('#smsqty').val();
			if (smsqty <= 0) {
				RhUI.toast.show('充值短信条数必须大于0');
				return
			}
			if (!$('#smsqty').isNumber()) {
				RhUI.toast.show('请输入数字');
				return
			}
			if (modal.minimumcharge > 0) {
				if (parseFloat(smsqty) < modal.minimumcharge) {
					RhUI.toast.show('最低充值短信为' + modal.minimumcharge + '条');
					return
				}
			}
			$('.btn-pay').attr('submit', 1);
			core.json('service/smsexpress', {
				smsqty:smsqty,compid:modal.compid,expid:modal.expid,iswxapp:0,payfrom:2
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