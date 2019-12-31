define(['core','tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {		
		$('#btnSubmit').click(function(){			
			var rid = $("#rid").val();
			var locationid = $("#locationid").val();
			var parkingid = $("#parkingid").val();
			if(rid==''){
			   RhUI.toast.show('房产不能为空');
			   return
			}
			if(locationid==''){
			   RhUI.toast.show('区域不能为空');
			   return
			}
			if(parkingid==''){
			   RhUI.toast.show('车位不能为空');
			   return
			}
			$("#myform").submit();			
		});
	};
	modal.initMonth = function (params) {
		modal.price = params.price;		
		modal.parkingid = params.parkingid;
		modal.feemonths = params.feemonths;
		modal.payfrom = params.payfrom;
		
		$(".minus").unbind('click').click(function () {
			var qty = $("#qty").val();	
			if(qty>modal.feemonths){
				qty = qty - 1;
				$("#qty").val(qty);
				var enddate = new Date($("#enddate").val());
				enddate.setMonth(enddate.getMonth() - 1);	
				var datestr = enddate.getFullYear()+'-'+((enddate.getMonth()<9?'0':'')+(enddate.getMonth()+1))+'-'+(enddate.getDate()<10?'0':'')+enddate.getDate();
				$("#enddate").val(datestr);
			}
        })
		$(".plus").unbind('click').click(function () {
			var qty = $("#qty").val();
			if(!$.isNumber(qty)){
				qty = 0;
			}
			qty =  qty*1 + 1;
			$("#qty").val(qty);	
			var enddate = new Date($("#enddate").val());
			enddate.setMonth(enddate.getMonth() + 1);
			var datestr = enddate.getFullYear()+'-'+((enddate.getMonth()<9?'0':'')+(enddate.getMonth()+1))+'-'+(enddate.getDate()<10?'0':'')+enddate.getDate();
			$("#enddate").val(datestr);
        })
		
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			var carno = $(".car_input").attr("data-pai");	
			if(carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				}				
			}
			else{
				RhUI.toast.show('车牌不能为空');
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位正确手机号');
				return
			}			
			if ($('#realname').isEmpty()) {
				RhUI.toast.show('请输入真实姓名');
				return
			}
			$('#btnSubmit').hide();
			$('#weixin-btn').show()
			$('#alipay-btn').show()
		});
		wx.miniProgram.getEnv(function(res) {
			if(res.miniprogram){
				modal.iswxapp=1;
			};
		});		
		$("#weixin-btn").click(function () {
			var carno = $(".car_input").attr("data-pai");
			var startdate = $("#startdate").val();
			var enddate = $("#enddate").val();
			var mobile = $("#mobile").val();
			var realname = $("#realname").val();
			var qty = $("#qty").val();	
			
			var money = modal.price*qty;
			if (isNaN(money) || money<=0){
				RhUI.toast.show('金额有误!');
				return;
			}
			
			RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
				 core.json('car/parklease', {parkingid:modal.parkingid,money:money,qty:qty,carno:carno,startdate:startdate,enddate:enddate,mobile:mobile,realname:realname,iswxapp:modal.iswxapp,payfrom:1}, function(rjson) {
						if (rjson.status != 1) {					
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
		$("#alipay-btn").click(function () {
			var carno = $(".car_input").attr("data-pai");
			var startdate = $("#startdate").val();
			var enddate = $("#enddate").val();
			var mobile = $("#mobile").val();
			var realname = $("#realname").val();
			var qty = $("#qty").val();	
			
			var money = modal.price*qty;
			if (isNaN(money) || money<=0){
				RhUI.toast.show('金额有误!');
				return;
			}
			
			RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
				 core.json('car/parklease', {parkingid:modal.parkingid,money:money,qty:qty,carno:carno,startdate:startdate,enddate:enddate,mobile:mobile,realname:realname,iswxapp:modal.iswxapp,payfrom:2}, function(rjson) {
						if (rjson.status != 1) {					
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
    };
	return modal
});