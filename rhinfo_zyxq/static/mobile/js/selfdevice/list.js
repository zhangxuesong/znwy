define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1,range:500,lat:0,lng:0,iswxapp:0,cateid:0,payfrom:1};
    modal.init = function (params) {      
		modal.range = params.range ? params.range : 500;
		modal.lat = params.lat;
		modal.lng = params.lng;	
		modal.cateid = params.cateid;	
		wx.miniProgram.getEnv(function(res) {
			if(res.miniprogram){
				modal.iswxapp=1;
			};
		});	    
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }		
    };
    modal.getList = function () { 
            core.json('selfdevice/list', {page: modal.page,lat: modal.lat, lng: modal.lng, range: modal.range,cateid:modal.cateid},
			function (ret) {
                var result = ret.result;
				var content = $(".container").html();  
                if (result.total <= 0) {
					if(content == null || content.length == 0) {
						$('.content-empty').show();
					}
                    $('.fui-content').infinite('stop')
                } else {
                    $('.content-empty').hide();
                    $('.container').show();
                    $('.fui-content').infinite('init');
                    if (result.list.length <= 0 || result.list.length < result.pagesize) {
                        $('.fui-content').infinite('stop')
                    }
                }
                modal.page++;
                core.tpl('.container', 'tpl_selfdevice_list', result, modal.page > 1);
				modal.bindEvents();
            }, false, true );
    }; 
    modal.bindEvents = function() {
		$('.btn-pay').unbind('click').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			var fee = $(this).attr("data-price");
			var selfid = $(this).attr("data-selfid");
			var that = $(this);
			RhUI.confirm('确认支付: ' + fee + " 元开始吗?",'支付确认',function(){				
				core.json('selfdevice/pay', {
					'selfid':selfid,
					'fee':fee,
					'iswxapp':modal.iswxapp,
					'payfrom':1
				}, function(rjson) {
					if (rjson.status == 0) {										
						RhUI.toast.show(rjson.result.message);
						return
					}	
					if (rjson.status == 2) {									
						RhUI.toast.show(rjson.result.message);
						return
					}
					var wechat = rjson.result.wechat;
					
					if(wechat==''){
						RhUI.toast.show('可以开始了');
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
								RhUI.toast.show('可以开始了');
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
			});
		});
	};
	modal.initMy = function(params) {
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getMylist()
			}
		});
		if (modal.page == 1) {
			modal.getMylist()		
		}		
	};
	modal.getMylist = function() {
		core.json('selfdevice/my',{page: modal.page},
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
			core.tpl('.container', 'tpl_myselfdevice_list', result, modal.page > 1);			
		},false,true);
	};
	modal.initScan = function (params) {  
		modal.payfrom = params.payfrom;
		if(modal.payfrom==2){
			modal.bindEvents_alipay();
		}
		else{
			wx.miniProgram.getEnv(function(res) {
				if(res.miniprogram){
					modal.iswxapp=1;
				};
			});	    
			modal.bindEvents();
		}
    };
    modal.bindEvents_alipay = function() {
		$('.btn-pay').unbind('click').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			var fee = $(this).attr("data-price");
			var selfid = $(this).attr("data-selfid");
			var that = $(this);
			RhUI.confirm('确认支付: ' + fee + " 元开始吗?",'支付确认',function(){				
				core.json('selfdevice/pay', {
					'selfid':selfid,
					'fee':fee,
					'iswxapp':modal.iswxapp,
					'payfrom':2
				}, function(rjson) {
					if (rjson.status == 0) {										
						RhUI.toast.show(rjson.result.message);
						return
					}	
					if (rjson.status == 2) {									
						RhUI.toast.show(rjson.result.message);
						return
					}
					var alipay = rjson.result.alipay;
					
					if(alipay==''){
						RhUI.toast.show('可以开始了');
						location.href = core.getUrl('service');
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
			});
		});
	};
    return modal
});