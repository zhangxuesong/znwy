define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		bid: 0,
		paydate:'',
		iswxapp:0
	};
	
	modal.init = function(params) {
		modal.bid = params.bid;
		modal.paydate = params.paydate;		
		
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#paydate').datePicker()
		});	
		
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getPayfee()
            }
        });
				
        if (modal.page == 1) {
            modal.getPayfee()
        }
	};
	modal.getPayfee = function() {		
		core.json('business/payfee', {
			page: modal.page,
			bid: modal.bid,
			paydate: modal.paydate
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_feebill_list', result, modal.page > 1);			
		})
	};
	modal.initCredit = function(params) {
		modal.bid = params.bid;
		modal.paydate = params.paydate;		
		
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#paydate').datePicker()
		});	
		
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getPaycredit()
            }
        });
				
        if (modal.page == 1) {
            modal.getPaycredit()
        }
	};
	modal.getPaycredit = function(params) {		
		core.json('business/paycredit', {
			page: modal.page,
			bid: modal.bid,
			paydate: modal.paydate
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_feebill_list', result, modal.page > 1);			
		})
	};
	modal.initPay = function(params) {	
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
					 core.json('business/pay', {id:bid,money:money,exchange:exchange,iswxapp:modal.iswxapp}, function(rjson) {
							if (rjson.status != 1) {
								$('#btn-pay').removeAttr('submit');							
								RhUI.toast.show(rjson.result.message);
								return
							}							
							var wechat = rjson.result.wechat;
							if(wechat==''){
								location.href = rjson.result.url;	
								return
							}			
							if(modal.iswxapp == 1){														
								var path = '/pages/mypay/pay?params=' + wechat;
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
										location.href = rjson.result.url;
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
	return modal
});