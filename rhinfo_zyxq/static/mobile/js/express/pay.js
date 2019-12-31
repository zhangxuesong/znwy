define(['core','tpl'], function(core, tpl) {
	var modal = {
		iswxapp:0,
		page: 1,
		cate:0,
		ctime:'',
		sid: 0,
		compid:0,
		today:0,
		cfrom:'',
		bstatus:0,
		money:0,
		billids:'',
		payfrom:1
	};	
	modal.init = function(params) {		
		modal.payfrom = params.payfrom;
		if(modal.payfrom==2){				
			$("#btn-pay").click(function () {
				var money = $.trim( $("#money").val());
				if (isNaN(money) || money<=0){
					RhUI.toast.show('请到店支付');
					return;
				}				   
				var sid = $.trim( $("#sid").val());	
		
				RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
					 core.json('expressp/pay', {sid:sid,money:money,iswxapp:modal.iswxapp,payfrom:2}, function(rjson) {
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
			});
		}
		else{
			wx.miniProgram.getEnv(function(res) {
				if(res.miniprogram){
					modal.iswxapp=1;
				};
			});		
			$("#btn-pay").click(function () {
				var money = $.trim( $("#money").val());
				if (isNaN(money) || money<=0){
					RhUI.toast.show('请到店支付');
					return;
				}				   
				var sid = $.trim( $("#sid").val());	
		
				RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
					 core.json('expressp/pay', {sid:sid,money:money,iswxapp:modal.iswxapp,payfrom:1}, function(rjson) {
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

			});
		}
	};
	modal.initTab = function(params) {
		modal.cate = params.cate;	
		modal.ctime = params.ctime;		
		modal.payfrom = params.payfrom;
		wx.miniProgram.getEnv(function(res) {
			if(res.miniprogram){
				modal.iswxapp=1;
			};
		});		

		$('.fui-content').infinite({
            onLoading: function () {
				if(modal.cate==1){
					modal.getList1();
				}
				if(modal.cate==2){
					modal.getList2();
				}
            }
        });
        if (modal.page == 1) {
          	if(modal.cate==1){
				modal.getList1();
			}
			if(modal.cate==2){
				modal.getList2();
			}
        }
		
		RhUI.tab({
			container: $('#tab'),
			handlers: {			
				tab1: function() {
					modal.changeTab(1)
				},
				tab2: function() {
					modal.changeTab(2)
				}
			}
		});
	};
	modal.changeTab = function(cate) {
		$('.fui-content-inner').infinite('init');
		$('.content-empty').hide();
		$('.container').html('');
		$('.infinite-loading').show();
		modal.page = 1;
		modal.cate = cate;		
		$("#cate").val(cate);
		modal.ctime = '';
		$("#ctime").val('');
		if(modal.cate==1){
			modal.getList1();
		}
		if(modal.cate==2){
			modal.getList2();
		}
	};
	modal.getList1 = function() {		
		core.json('expressp/myexpress', {
			page: modal.page,
			cate: modal.cate
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
			core.tpl('.container', 'tpl_express_send_list', result, modal.page > 1);
			$('.fui-card').find('.payexpress').unbind('click').click(function() {
				var money = $(this).data('money');
				var sid = $(this).data('sid');
				var expressid = $(this).data('eid');
				
				if (isNaN(money) || money<=0){
					RhUI.toast.show('请到店支付');
					return;
				}				   
				if(modal.payfrom==2){
					RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
					 core.json('expressp/pay', {sid:sid,money:money,expressid:expressid,iswxapp:modal.iswxapp,payfrom:2}, function(rjson) {
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
					 core.json('expressp/pay', {sid:sid,money:money,expressid:expressid,iswxapp:modal.iswxapp,payfrom:1}, function(rjson) {
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
			});
		})
	};
	modal.getList2 = function() {		
		core.json('expressp/myexpress', {
			page: modal.page,
			cate: modal.cate
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop');				
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_express_take_list', result, modal.page > 1);	
			$('.fui-card').find('.express').unbind('click').click(function() {
				var obj = $(this);
				var qrcode = obj.data('qrcode');
				$("#eqrcode").html("<img src='" + qrcode + "' width='150px;'/>");
				var html = $(".pop-express-hidden").html();
				var container = new RhUIModal({
					content: html, extraClass: "popup-modal", maskClick: function () {
						container.close()
					}
				});
				container.show();
				$('.verify-pop').find('.close').unbind('click').click(function () {
					container.close()
				});  		
			});
		})
	};
	modal.initMoney = function(params) {
		modal.sid = params.sid;
		modal.compid = params.compid;
		modal.today = params.today;
		modal.bstatus = params.bstatus;
		modal.cfrom = params.cfrom;
		modal.money = params.money;
		modal.billids = params.billids;
		modal.payfrom = params.payfrom;
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getExpress()
            }
        });
				
        if (modal.page == 1) {
            modal.getExpress()
        }
		$("#btn-recharge").click(function () {			  	
			RhUI.confirm('确认已收款: ' + modal.money + " 元?",'收款确认',function(){					
				 core.json('express/rechargemoney', {sid:modal.sid,compid:modal.compid,money:modal.money,billids:modal.billids,iswxapp:modal.iswxapp}, function(rjson) {
						if (rjson.status != 1) {
							$('#btn-pay').removeAttr('submit');							
							RhUI.toast.show(rjson.result.message);
							return
						}
						RhUI.toast.show('操作成功');
						location.href = core.getUrl('express/money') + '&sid=' + modal.sid;
						return
				 });
			});
		});
		$("#btn-pay").click(function () {
			if (isNaN(modal.money) || modal.money<=0){
				RhUI.toast.show('金额有误!');
				return;
			}   
			if(modal.payfrom==2){
				RhUI.confirm('确认支付金额为: ' + modal.money + " 元?",'支付确认',function(){					
					 core.json('express/paymoney', {sid:modal.sid,compid:modal.compid,money:modal.money,billids:modal.billids,iswxapp:modal.iswxapp,payfrom:2}, function(rjson) {
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
				RhUI.confirm('确认支付金额为: ' + modal.money + " 元?",'支付确认',function(){					
					 core.json('express/paymoney', {sid:modal.sid,compid:modal.compid,money:modal.money,billids:modal.billids,iswxapp:modal.iswxapp,payfrom:1}, function(rjson) {
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
		});
	}
	modal.getExpress = function() {	
		core.json('express/express_fee', {
			page: modal.page,
			sid: modal.sid,
			compid:modal.compid,
			today:modal.today,
			cfrom:modal.cfrom,
			bstatus:modal.bstatus
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.onekey').hide();
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
			core.tpl('.container', 'tpl_express_list', result, modal.page > 1);	
		})
	};
	return modal
});