define(['core','tpl'], function(core, tpl) {
	var modal = {rid:0,page:1,lat:'',lng:'',range:500,parklockid:0,iswxapp:0,payfrom:1};
	modal.init = function(params) {	
		modal.rid = params.rid;
		$('#btnSubmit').click(function(){
			if ($('#btnSubmit').attr('stop')) {
				return
			}		
			if ($('#title').isEmpty()) {
			   RhUI.toast.show('车锁名称不能为空');
			   return
			}
			if ($('#lockmac').isEmpty()) {
			   RhUI.toast.show('车锁MAC地址不能为空');
			   return
			}			
			var parkingid = $.trim($("input[name=parkingid]:checked").val());
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			core.json('car/addlock',{
				 rid:modal.rid,
				 parkingid:parkingid,
				 title:$("#title").val(),
				 lockmac:$("#lockmac").val(),
				 remark:$("#remark").val()
			}, function(rjson) {
				if (rjson.status != 1) {					
					RhUI.toast.show(rjson.result.message);
					$('#btnSubmit').html('提交').removeAttr('stop');
					return
				}
				RhUI.toast.show(rjson.result.message);
				location.href = core.getUrl('car/mylock');
			})
		})
	};
	modal.initCtrl = function (params) {
		$('*[data-toggle=delete]').unbind('click').click(function() {
			var item = $(this).closest('.address-item');
			var id = item.data('addressid');
			RhUI.confirm('删除后无法恢复, 确认要删除吗 ?', function() {
				core.json('car/lockdelete', {
					id: id
				}, function(ret) {
					if (ret.status == 1) {						
						item.remove();
						setTimeout(function() {
							if ($(".address-item").length <= 0) {
								$('.content-empty').show()
							}
						}, 100);
						return
					}
					RhUI.toast.show(ret.result.message)
				}, true, true)
			})
		});	
		$('*[data-toggle=control]').unbind('click').click(function() {
			var self = $(this);
			var id = self.data('lockid');
			var online =  self.data('online');
			if(online==0){
			   RhUI.toast.show('不在线');
			   return
			}
			var updown =  self.data('updown');
			var updowntxt1 = updown==1?'上升':'下降';
			var updowntxt2 = updown==1?'下降':'上升';
			if(updown==1){
				updown = 0;
			}
			else{
				updown = 1;
			}
			RhUI.confirm('确认要' + updowntxt1 + '操作吗 ?', function() {
				self.html('<i class="icon icon-yaochi text-warning"></i>' + updowntxt1); 
				core.json('car/ctrllock', {
					id: id,
					updown:updown
				}, function(ret) {
					if (ret.status == 0) {
						self.html('<i class="icon icon-warn text-warning"></i>' + updowntxt1);
						RhUI.toast.show(ret.result.message);
						setTimeout(function(){self.html('<i class="icon icon-lock text-warning"></i>' + updowntxt1); },2000);
						return
					}
					self.html('<i class="icon icon-unlock text-warning"></i>' + updowntxt2); 
					self.data('updown',updown);
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.html('<i class="icon icon-lock text-warning"></i> '+ updowntxt2); },2000);
				}, false, true);
			});
		});
    };
	modal.initShare = function(params) {
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#starttime').datetimePicker();
			$('#endtime').datetimePicker();
		});	
		$('#btnSubmit').click(function(){						
			$("#myform").submit();			
		});
	};
	modal.initGrant = function(params) {
		modal.parklockid = params.parklockid;	
		$('#btn-share').click(function() {
				$('#cover').fadeIn(200)
			});
		$('#cover').click(function() {
			$('#cover').hide()
		})
		$("#btn-payshare").on('click',function(){
			$(".pop-parkshare-hidden").show();
			$("#price").focus();
		    $('.verify-pop').find('.btn-close').unbind('click').click(function () {
                $(".pop-parkshare-hidden").hide();
            }); 
			$('.verify-pop').find('.btn-submit').unbind('click').click(function () {
				if($('#price').isEmpty()){
					RhUI.toast.show('请输入价格');
					return
				}
				core.json('car/shareprice', {
					'price': $('#price').val(),
					'remark':$('#remark').val(),
					'parklockid': modal.parklockid
					}, function(ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);					
							return
						}
						RhUI.toast.show(ret.result.message);
						setTimeout(function(){
							location.href=core.getUrl('car/sharelist');
						},2000);
					}, true, true);	
			});
		})
	};
	modal.initSharelist = function(params) {
		modal.range = params.range;
		modal.lat = params.lat;
		modal.lng = params.lng;
		modal.payfrom = params.payfrom;
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
		core.json('car/sharelist', {page: modal.page, lat: modal.lat, lng: modal.lng,range:modal.range}, function (ret) {
			var result = ret.result;
			var content = $(".container").html();  
			if (result.total <= 0) {
				if(content == null || content.length == 0) {
					$('.content-empty').show();
				}
				$('.fui-content').infinite('stop')
			} 
			else {
				$('.content-empty').hide();
				$('.container').show();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_parkshare_list', result, modal.page > 1);
			modal.bindEvents();
		}, false, true );
    };
	  modal.bindEvents = function() {		  
		$('.btn-pay').unbind('click').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			var fee = $(this).attr("data-price");
			var shareid = $(this).attr("data-shareid");	
			var that = $(this);
			if(modal.payfrom==1){
				RhUI.confirm('确认支付: ' + fee + " 元开始吗?",'支付确认',function(){				
					core.json('car/sharepay', {
						'shareid':shareid,
						'fee':fee,
						'iswxapp':modal.iswxapp
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
									RhUI.toast.show('支付成功');
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
			}
			if(modal.payfrom==2){
				RhUI.confirm('确认支付: ' + fee + " 元开始吗?",'支付确认',function(){				
					core.json('car/sharepay', {
						'shareid':shareid,
						'fee':fee,
						'iswxapp':modal.iswxapp
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
			}
		});
	};
	return modal
});