define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		id:0,page: 1,iswxapp:0,paytype:1,payfrom:1
	};
	modal.init = function(params) {
		modal.id = params.id;
		modal.paytype = params.paytype;
		modal.payfrom = params.payfrom;
		
		core.json('charging/getports',{id:modal.id},
		function(ret){
			var result = ret.result;			
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} 
			else {
				$('.content-empty').hide();
				$('.fui-content').infinite('stop')
			}	
			modal.page++;
			core.tpl('.charging-port', 'tpl_charging_port', result, modal.page > 1);
			
			$(".charging-port a").on("click", function() {
			var portsta = $(this).attr("data-status");
			if(portsta==1){
				RhUI.toast.show('该端口不可用');
				return
			}
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".charging-port a").removeClass("active");
				$(this).addClass("active");
				$("#category_port").val($(this).attr("data-port"))
			}
			
		});
		},false,true);					
		
		$(".charging-hour a").on("click", function() {
			
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".charging-hour a").removeClass("active");
				$(this).addClass("active");
				$("#category_hour").val($(this).attr("data-hour"))
			}
		});
		
		$('.verify-pop').find('.icon-roundclose').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
        });
		
		if(modal.payfrom==2){						
			$('.btn-submit').click(function() {
				if ($(this).attr('stop')) {
					return
				}
				if ($('#category_port').isEmpty()) {
					RhUI.toast.show('请选择设备端口');
					return
				}
				if(modal.paytype==1){
					if ($('#category_hour').isEmpty()) {
						RhUI.toast.show('充电时间不能为空!');
						return
					}
				}			
				var port = $('#category_port').val();
				var rule = $('#category_hour').val();
				
				var that = $(this);
				
				RhUI.confirm('确认充电插头已接好？','支付确认',function(){					
					that.html('正在处理...').attr('stop', 1);						
					core.json('charging/pay', {
						'chargid':$("#chargid").val(),
						'port':port,
						'rule':rule,
						'iswxapp':modal.iswxapp,
						'payfrom':2
					}, function(rjson) {
						if (rjson.status == 0) {
							that.html('提交').removeAttr('stop');					
							RhUI.toast.show(rjson.result.message);
							return
						}
						
						if (rjson.status == 2) {									
							RhUI.toast.show(rjson.result.message);
							return
						}
						
						var alipay = rjson.result.alipay;
						
						if(alipay==''){
							RhUI.toast.show('充电开始');
							location.href = core.getUrl('home/index');
							return
						}					
						
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
				})
			})
		}
		else{
			wx.miniProgram.getEnv(function(res) {
				if(res.miniprogram){
					modal.iswxapp=1;
				};
			});	    
			
			$('.btn-submit').click(function() {
				if ($(this).attr('stop')) {
					return
				}
				if ($('#category_port').isEmpty()) {
					RhUI.toast.show('请选择设备端口');
					return
				}
				if(modal.paytype==1){
					if ($('#category_hour').isEmpty()) {
						RhUI.toast.show('充电时间不能为空!');
						return
					}
				}			
				var port = $('#category_port').val();
				var rule = $('#category_hour').val();
				
				var that = $(this);
				
				RhUI.confirm('确认充电插头已接好？','支付确认',function(){					
					that.html('正在处理...').attr('stop', 1);						
					core.json('charging/pay', {
						'chargid':$("#chargid").val(),
						'port':port,
						'rule':rule,
						'iswxapp':modal.iswxapp,
						'payfrom':1
					}, function(rjson) {
						if (rjson.status == 0) {
							that.html('提交').removeAttr('stop');					
							RhUI.toast.show(rjson.result.message);
							return
						}
						
						if (rjson.status == 2) {									
							RhUI.toast.show(rjson.result.message);
							return
						}
						
						var wechat = rjson.result.wechat;
						
						if(wechat==''){
							RhUI.toast.show('充电开始');
							location.href = core.getUrl('home/index');
							return
						}
						
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
									RhUI.toast.show('充电开始');
									location.href = wechat.returl;
									return
								} 
								else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
									that.html('提交').removeAttr('stop');	
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
				})
			})
		}
	};	
	modal.initScan = function(params) {
		modal.paytype = params.paytype;
		modal.payfrom = params.payfrom;
		
		$(".charging-port a").on("click", function() {
			var portsta = $(this).attr("data-status");
			if(portsta==1){
				RhUI.toast.show('该端口不可用');
				return
			}
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".charging-port a").removeClass("active");
				$(this).addClass("active");
				$("#category_port").val($(this).attr("data-port"))
			}
		});
		
		$(".charging-hour a").on("click", function() {
			
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".charging-hour a").removeClass("active");
				$(this).addClass("active");
				$("#category_hour").val($(this).attr("data-hour"))
			}
		});
		
		$('.verify-pop').find('.icon-roundclose').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
        });
		if(modal.payfrom==2){			
			$('.btn-submit').click(function() {
				if ($(this).attr('stop')) {
					return
				}
				if ($('#category_port').isEmpty()) {
					RhUI.toast.show('请选择设备端口');
					return
				}
				if(modal.paytype==1){
					if ($('#category_hour').isEmpty()) {
						RhUI.toast.show('充电时间不能为空!');
						return
					}
				}		
				var port = $('#category_port').val();
				var rule = $('#category_hour').val();
				
				var that = $(this);
				
				RhUI.confirm('确认充电插头已接好？','支付确认',function(){					
					that.html('正在处理...').attr('stop', 1);						
					core.json('charging/pay', {
						'chargid':$("#chargid").val(),
						'port':port,
						'rule':rule,
						'iswxapp':modal.iswxapp,
						'payfrom':1
					}, function(rjson) {
						if (rjson.status == 0) {
							that.html('提交').removeAttr('stop');					
							RhUI.toast.show(rjson.result.message);
							return
						}
						var alipay = rjson.result.alipay;
							
						if(alipay==''){
							RhUI.toast.show('充电开始');
							location.href = core.getUrl('home/index');
							return
						}					
						
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
				})
			})
		}
		else{
			wx.miniProgram.getEnv(function(res) {
				if(res.miniprogram){
					modal.iswxapp=1;
				};
			});	 	    
			
			$('.btn-submit').click(function() {
				if ($(this).attr('stop')) {
					return
				}
				if ($('#category_port').isEmpty()) {
					RhUI.toast.show('请选择设备端口');
					return
				}
				if(modal.paytype==1){
					if ($('#category_hour').isEmpty()) {
						RhUI.toast.show('充电时间不能为空!');
						return
					}
				}		
				var port = $('#category_port').val();
				var rule = $('#category_hour').val();
				
				var that = $(this);
				
				RhUI.confirm('确认充电插头已接好？','支付确认',function(){					
					that.html('正在处理...').attr('stop', 1);						
					core.json('charging/pay', {
						'chargid':$("#chargid").val(),
						'port':port,
						'rule':rule,
						'iswxapp':modal.iswxapp,
						'payfrom':1
					}, function(rjson) {
						if (rjson.status == 0) {
							that.html('提交').removeAttr('stop');					
							RhUI.toast.show(rjson.result.message);
							return
						}
						
						if (rjson.status == 2) {									
							RhUI.toast.show(rjson.result.message);
							location.href = core.getUrl('charging/my');
							return
						}
						
						var wechat = rjson.result.wechat;
						
						if(wechat==''){
							RhUI.toast.show('充电开始');
							location.href = core.getUrl('service');
							return
						}
						
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
									RhUI.toast.show('充电开始');
									location.href = wechat.returl;
									return
								} 
								else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
									that.html('提交').removeAttr('stop');	
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
				})
			})
		}
	};	
	modal.initMy = function(params) {
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList()
			}
		});
		if (modal.page == 1) {
			modal.getList()			
		}
	}
	modal.getList = function() {
		core.json('charging/my',{page: modal.page},
		function(ret){
			var result = ret.result;			
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} 
			else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_mycharging_list', result, modal.page > 1);	
			modal.bindEvents();
		},false,true);
	};
	modal.bindEvents = function() {
		$('.btn-restart').unbind('click').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			var logid = $(this).attr("data-logid");			
			var that = $(this);
			$(this).html('正在开启').attr('stop', 1);
			core.json('charging/restart', {
				'logid':logid
			}, function(rjson) {
				if (rjson.status == 1) {
					that.html('重新开启').removeAttr('stop');					
					RhUI.toast.show(rjson.result.message);
					return
				}
				that.html('<span class="text-success">充电开始</span>');					
				RhUI.toast.show('开启成功');
			}, false, true)
		})
	};
	modal.initAddcard = function(params) {
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if ($("#cardno").isEmpty()) {
				RhUI.toast.show('请输入卡片编号');
				return
			}
			
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			
			core.json('charging/addcard', {
				cardno:$("#cardno").val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('提交').removeAttr('stop');
					return
				}
				RhUI.alert(ret.result.message, '', function() {
					location.href = core.getUrl('charging/vipcard');
				})
			}, false, true)
		});
	}
	modal.initMycard = function(params) {
		$('*[data-toggle=delete]').unbind('click').click(function() {
			var item = $(this).closest('.card-item');
			var id = item.data('cardid');
			RhUI.confirm('删除后无法恢复, 确认要删除吗 ?', function() {
				core.json('charging/delcard', {
					id: id
				}, function(ret) {
					if (ret.status == 1) {						
						item.remove();
						setTimeout(function() {
							if ($(".card-item").length <= 0) {
								$('.content-empty').show();
								$('.fui-list-group').hide();
							}
						}, 100);
						return
					}
					RhUI.toast.show(ret.result.message)
				}, true, true)
			})
		});		
	};
	return modal
});