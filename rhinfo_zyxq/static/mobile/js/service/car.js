define(['core'], function(core) {
	var modal = {page:1,endtime:0,id:0,seconds:60,keyword:'',lat:'',lng:'',money:0,pid:0,rid:0,parkid:0,parktype:0,iswxapp:0,carno:'',range:500,recordid:0,price:0,hours:0,method:0,ioid:0,intime:'',isauth:'',post:'addcar',payfrom:1};	
	modal.init = function(params) {
		modal.post = params.post;
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#checkdate').datePicker()
		});	

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
			
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			
			core.json('service/'+ modal.post, {
				checkdate: $('#checkdate').val(),
				safeno: $('#safeno').val(),
				remark: $('#remark').val(),
				id:$('#carid').val(),
				carno:carno
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('提交').removeAttr('stop');
					return
				}
				RhUI.alert(ret.result.message, '', function() {
					location.href = core.getUrl('service/mycar');
				})
			}, false, true)
		});
	};
	modal.initList = function() {		
		$('*[data-toggle=delete]').unbind('click').click(function() {
			var item = $(this).closest('.car-item');
			var id = item.data('carid');
			RhUI.confirm('删除后无法恢复, 确认要删除吗 ?', function() {
				core.json('service/delcar', {
					id: id
				}, function(ret) {
					if (ret.status == 1) {						
						item.remove();
						setTimeout(function() {
							if ($(".car-item").length <= 0) {
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
	modal.initIndex = function(params) {	
		modal.endtime = params.endtime;
		modal.id = params.id;

		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位正确手机号码');
				return
			}
			
			core.json('car/verifycode', {
				mobile: $('#mobile').val(),
				id:modal.id
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
				}
				if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
			}, false, true)
		});
		$('.fui-uploader').click(function(){				
			core.wxUploadImages($(this),"images",core.getUrl('util/wxuploader'),core.getUrl('util/remove'));	
		});
		
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}			
			var carno = $("#carno").val();
			
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
				RhUI.toast.show('请输入11位正确手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			
			core.json('car/moveadd', {
				'images': images,
				'remark': $('#remark').val(),
				'carno':carno,
				'mobile': $('#mobile').val(),
				'verifycode': $('#verifycode').val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('提交').removeAttr('stop');
					return
				}
				RhUI.alert(ret.result.message, '', function() {
					location.href = core.getUrl('service');
				})
			}, false, true)
		});
	};
	modal.initMove = function(params) {	
		modal.endtime = params.endtime;

		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位正确手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('car/verifycode', {
				mobile: $('#mobile').val()
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
				}
				if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
			}, false, true)
		});
		$('.fui-uploader').click(function(){				
			core.wxUploadImages($(this),"images",core.getUrl('util/wxuploader'),core.getUrl('util/remove'));	
		});
		
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
				RhUI.toast.show('请输入11位正确手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			
			core.json('car/moveadd', {
				'images': images,
				'remark': $('#remark').val(),
				'carno':carno,
				'mobile': $('#mobile').val(),
				'verifycode': $('#verifycode').val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('提交').removeAttr('stop');
					return
				}
				RhUI.alert(ret.result.message, '', function() {
					location.href = core.getUrl('service');
				})
			}, false, true)
		});
	};
	modal.verifycode = function() {
		modal.seconds--;
		if (modal.seconds > 0) {
			$('#btnCode').html(modal.seconds + '秒后重发').addClass('disabled').attr('disabled', 'disabled');
			setTimeout(function() {
				modal.verifycode()
			}, 1000)
		} else {
			$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
		}
	};
	modal.initMovecfm = function(params) {
		modal.params = params;
		core.showImages('.myimg img');
		$("#btnSubmit").click(function(){
			core.json('car/movecfm', {
				'reply': $('#reply').val(),
				'id':modal.params.id
			}, function(ret) {
				WeixinJSBridge.call("closeWindow");
			}, false, true)
		});
	};
	modal.initCarno = function(params) {
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
			
			var parkid = $("#parkid").attr("data-parkid");
			location.href = core.getUrl('car/pay')+ "&parkid=" + parkid + "&carno=" + carno;
		});
	};
	modal.initPark = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;		
		modal.lat = params.lat ? params.lat :'';
		modal.lng = params.lng ? params.lng : '';
		modal.carno = params.carno ? params.carno : '';
		modal.range = params.range ? params.range: 500;
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
            var _this = this;            
            core.json('car/parklist', {page: modal.page, keyword: modal.keyword, lat: modal.lat, lng: modal.lng,carno:modal.carno,range:modal.range}, function (ret) {
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
                core.tpl('.container', 'tpl_parking_list', result, modal.page > 1);
            }, false, true );
    };
	modal.initPay = function(params) {	
		modal.money = params.money;
		modal.pid = params.pid;
		modal.rid = params.rid;
		modal.parkid = params.parkid;
		modal.recordid = params.recordid;
		modal.intime = params.intime;
		modal.payfrom = params.payfrom;
		
		if(modal.payfrom ==2){			
			$("#btn-pay").click(function () {
				if ($('#btn-pay').attr('stop')) {
					return
				}
				if (isNaN(modal.money) || modal.money<=0){
					RhUI.toast.show('金额有误!');
					return;
				}				   
				var carno = $(".car_input").attr("data-pai");
				RhUI.confirm('确认支付金额为: ' + modal.money + " 元?",'支付确认',function(){
					$('#btn-pay').attr('stop', 1);
					 core.json('car/pay', {pid:modal.pid,rid:modal.rid,parkid:modal.parkid,money:modal.money,recordid:modal.recordid,intime:modal.intime,carno:carno,iswxapp:modal.iswxapp,payfrom:2}, function(rjson) {
						if (rjson.status != 1) {
							$('#btn-pay').removeAttr('stop');							
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
					$("#btn-alipay").hide();
				};
			});		
			$("#btn-pay").click(function () {
				if ($('#btn-pay').attr('stop')) {
					return
				}
				if (isNaN(modal.money) || modal.money<=0){
					RhUI.toast.show('金额有误!');
					return;
				}				   
				var carno = $(".car_input").attr("data-pai");
				RhUI.confirm('确认支付金额为: ' + modal.money + " 元?",'支付确认',function(){
					$('#btn-pay').attr('stop', 1);
					 core.json('car/pay', {pid:modal.pid,rid:modal.rid,parkid:modal.parkid,money:modal.money,recordid:modal.recordid,intime:modal.intime,carno:carno,iswxapp:modal.iswxapp,payfrom:1}, function(rjson) {
						if (rjson.status != 1) {
							$('#btn-pay').removeAttr('stop');							
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
		$("#btn-alipay").click(function () {
			if ($('#btn-alipay').attr('stop')) {
				return
			}
			if (isNaN(modal.money) || modal.money<=0){
				RhUI.toast.show('金额有误!');
				return;
			}				   
			var carno = $(".car_input").attr("data-pai");
			RhUI.confirm('确认支付金额为: ' + modal.money + " 元?",'支付确认',function(){
				$('#btn-alipay').attr('stop', 1);
				 core.json('car/pay', {pid:modal.pid,rid:modal.rid,parkid:modal.parkid,money:modal.money,recordid:modal.recordid,intime:modal.intime,carno:carno,payfrom:2,iswxapp:modal.iswxapp}, function(rjson) {
						if (rjson.status != 1) {
							$('#btn-alipay').removeAttr('stop');							
							RhUI.toast.show(rjson.result.message);
							return
						}
						if(rjson.result.alipay==''){
							RhUI.toast.show('参数错误');
							return
						}
						location.href = core.getUrl('alipay') + '&alipayurl=' + rjson.result.alipay;
						
					}, false, true);
			});
		});
	};
	modal.initMonth = function (params) {
		modal.price = params.price;		
		modal.parkid = params.parkid;
		modal.parktype = params.parktype;
		modal.method = params.monthmethod;
		modal.isauth = params.authentication;
		modal.payfrom = params.payfrom;
		
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位正确手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('car/verifycode', {
				mobile: $('#mobile').val()
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
				}
				if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
			}, false, true)
		});
		$(".minus").unbind('click').click(function () {
			if(modal.method==2 || modal.method==4){
				return 
			}
			var qty = $("#qty").val();	
			if(qty>1){
				qty = qty - 1;
				$("#qty").val(qty);
				var enddate = new Date($("#enddate").val());
				if(modal.method==0){
					enddate.setMonth(enddate.getMonth() - 1);
				}
				else{
					 var new_year = enddate.getFullYear();  //取当前的年份          
					 var new_month = enddate.getMonth();//取当前的月份    
					 var new_date = new Date(new_year,new_month,1);  //取当年当月中的第一天 					 
					 if(new_month==1) {         
						new_month =12;           
						new_year--;           
					 }
					 else{
						 new_month--;
					 }
					var last_day = (new Date(new_date.getTime()-1000*60*60*24)).getDate();
					enddate = new Date(new_year,new_month,last_day);//获取当月最后一天日
				}				
				var datestr = enddate.getFullYear()+'-'+((enddate.getMonth()<9?'0':'')+(enddate.getMonth()+1))+'-'+(enddate.getDate()<10?'0':'')+enddate.getDate();
				$("#enddate").val(datestr);
			}
        })
		$(".plus").unbind('click').click(function () {
			if(modal.method==2 || modal.method==4){
				return 
			}
			var qty = $("#qty").val();
			if(!$.isNumber(qty)){
				qty = 0;
			}
			qty =  qty*1 + 1;
			$("#qty").val(qty);	
			var enddate = new Date($("#enddate").val());
			if(modal.method==0){
				enddate.setMonth(enddate.getMonth() + 1);
			}
			else{
				 var new_year = enddate.getFullYear();    //取当前的年份          
				 var new_month = enddate.getMonth();//取当前的月份   
				if(new_month==12) {         
					new_month =1;               
					new_year++; 
				} 
				else{
					new_month++;
				}
				var new_date = new Date(new_year,new_month + 1,1);  //取当年下月中的第一天
				var last_day = (new Date(new_date.getTime()-1000*60*60*24)).getDate();
				enddate = new Date(new_year,new_month,last_day);//获取当月最后一天日
			}			
			var datestr = enddate.getFullYear()+'-'+((enddate.getMonth()<9?'0':'')+(enddate.getMonth()+1))+'-'+(enddate.getDate()<10?'0':'')+enddate.getDate();
			$("#enddate").val(datestr);
        })
		
		$('#btnSubmit').click(function() {
			if(modal.parktype==1){
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
			}
			if(!$("#startdate").isDate() || !$("#enddate").isDate()){
				RhUI.toast.show('日期不能为空');
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
			if(modal.isauth>0){
				if (!$('#idcard').isIDCard()) {
					RhUI.toast.show('请输入正确身份证号码');
					return
				}
				if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
					RhUI.toast.show('请输入5位数字验证码');
					return
				}
			}
			$('#btnSubmit').hide();
			$('#mypay-btn').show()
		});
		
		if(modal.payfrom ==2){				
			$("#weixin-btn").click(function () {
				if ($('.btn-pay').attr('submit')) {
					return
				}
				var carno = $(".car_input").attr("data-pai");
				var startdate = $("#startdate").val();
				var enddate = $("#enddate").val();
				var mobile = $("#mobile").val();
				var realname = $("#realname").val();
				var qty = $("#qty").val();
				var idcard = $("#idcard").val();
				var verifycode = $("#verifycode").val();
				if(modal.method==0 || modal.method==2 || modal.method==4){
					var money = modal.price*qty;
				}
				else{
					var startdate1 = new Date(startdate);
					if(startdate1.getDate()>15){
						var money = modal.price*(qty-0.5);
					}
					else{
						var money = modal.price*qty;
					}
				}
				
				if (isNaN(money) || money<=0){
					RhUI.toast.show('金额有误!');
					return;
				}				   
				var urlstr = 'car/monthcard';
				if(modal.parktype==2){
					urlstr = 'car/monthcard2';
				}
				RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){	
					 $('.btn-pay').attr('submit', 1);
					 core.json(urlstr, {parkid:modal.parkid,money:money,qty:qty,carno:carno,startdate:startdate,enddate:enddate,mobile:mobile,realname:realname,idcard:idcard,verifycode:verifycode,payfrom:2,iswxapp:modal.iswxapp}, function(rjson) {
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
			
			$("#weixin-btn").click(function () {
				if ($('.btn-pay').attr('submit')) {
					return
				}
				var carno = $(".car_input").attr("data-pai");
				var startdate = $("#startdate").val();
				var enddate = $("#enddate").val();
				var mobile = $("#mobile").val();
				var realname = $("#realname").val();
				var qty = $("#qty").val();
				var idcard = $("#idcard").val();
				var verifycode = $("#verifycode").val();
				if(modal.method==0 || modal.method==2 || modal.method==4){
					var money = modal.price*qty;
				}
				else{
					var startdate1 = new Date(startdate);
					if(startdate1.getDate()>15){
						var money = modal.price*(qty-0.5);
					}
					else{
						var money = modal.price*qty;
					}
				}
				
				if (isNaN(money) || money<=0){
					RhUI.toast.show('金额有误!');
					return;
				}				   
				var urlstr = 'car/monthcard';
				if(modal.parktype==2){
					urlstr = 'car/monthcard2';
				}
				RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){	
					 $('.btn-pay').attr('submit', 1);
					 core.json(urlstr, {parkid:modal.parkid,money:money,qty:qty,carno:carno,startdate:startdate,enddate:enddate,mobile:mobile,realname:realname,idcard:idcard,verifycode:verifycode,payfrom:1,iswxapp:modal.iswxapp}, function(rjson) {
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
					}, false, true);
				});
			});
		}
		$("#alipay-btn").click(function () {
			var carno = $(".car_input").attr("data-pai");
			var startdate = $("#startdate").val();
			var enddate = $("#enddate").val();
			var mobile = $("#mobile").val();
			var realname = $("#realname").val();
			var qty = $("#qty").val();
			var idcard = $("#idcard").val();
			var verifycode = $("#verifycode").val();
			if(modal.method==0 || modal.method==2 || modal.method==4){
				var money = modal.price*qty;
			}
			else{
				var startdate1 = new Date(startdate);
				if(startdate1.getDate()>15){
					var money = modal.price*(qty-0.5);
				}
				else{
					var money = modal.price*qty;
				}
			}
			
			if (isNaN(money) || money<=0){
				RhUI.toast.show('金额有误!');
				return;
			}				   
			var urlstr = 'car/monthcard';
			if(modal.parktype==2){
				urlstr = 'car/monthcard2';
			}
			RhUI.confirm('确认支付金额为: ' + money + " 元?",'支付确认',function(){					
				 core.json(urlstr, {parkid:modal.parkid,money:money,qty:qty,carno:carno,startdate:startdate,enddate:enddate,mobile:mobile,realname:realname,idcard:idcard,verifycode:verifycode,payfrom:2,iswxapp:modal.iswxapp}, function(rjson) {
					if (rjson.status != 1) {					
						RhUI.toast.show(rjson.result.message);
						return
					}
					if(rjson.result.alipay==''){
						RhUI.toast.show('参数错误');
						return
					}
					location.href = core.getUrl('alipay') + '&alipayurl=' + rjson.result.alipay;
				}, false, true);
			});
		});
	};
	modal.initPayio = function(params) {	
		modal.money = params.money;
		modal.pid = params.pid;
		modal.rid = params.rid;
		modal.parkid = params.parkid;
		modal.ioid = params.ioid;
		modal.recordid = params.recordid;
		modal.intime = params.intime;
		modal.payfrom = params.payfrom;
		
		if(modal.payfrom ==2){			
			$("#btn-pay").click(function () {
				if ($('#btn-pay').attr('submit')) {
					return
				}
				if (isNaN(modal.money) || modal.money<=0){
					RhUI.toast.show('金额有误!');
					return;
				}		
				RhUI.confirm('确认支付金额为: ' + modal.money + " 元?",'支付确认',function(){	
					 $('#btn-pay').attr('submit', 1);
					 core.json('service/scanpay', {pid:modal.pid,rid:modal.rid,parkid:modal.parkid,money:modal.money,recordid:modal.recordid,ioid:modal.ioid,intime:modal.intime,payfrom:2,iswxapp:modal.iswxapp}, function(rjson) {
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
				if ($('#btn-pay').attr('submit')) {
					return
				}
				if (isNaN(modal.money) || modal.money<=0){
					RhUI.toast.show('金额有误!');
					return;
				}		
				RhUI.confirm('确认支付金额为: ' + modal.money + " 元?",'支付确认',function(){	
					 $('#btn-pay').attr('submit', 1);
					 core.json('service/scanpay', {pid:modal.pid,rid:modal.rid,parkid:modal.parkid,money:modal.money,recordid:modal.recordid,ioid:modal.ioid,intime:modal.intime,payfrom:1,iswxapp:modal.iswxapp}, function(rjson) {
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
		$("#btn-alipay").click(function () {
			if ($('#btn-alipay').attr('submit')) {
				return
			}
			if (isNaN(modal.money) || modal.money<=0){
				RhUI.toast.show('金额有误!');
				return;
			}		
			RhUI.confirm('确认支付金额为: ' + modal.money + " 元?",'支付确认',function(){
				 $('#btn-alipay').attr('submit', 1);
				 core.json('service/scanpay', {pid:modal.pid,rid:modal.rid,parkid:modal.parkid,money:modal.money,recordid:modal.recordid,ioid:modal.ioid,intime:modal.intime,payfrom:2,iswxapp:modal.iswxapp}, function(rjson) {
					if (rjson.status != 1) {
						$('#btn-alipay').removeAttr('submit');							
					//	RhUI.toast.show(rjson.result.message);
						RhUI.toast.show('请检查支付宝参数'); 
						return
					}
					if(rjson.result.alipay==''){
						RhUI.toast.show('参数错误');
						return
					}
					location.href = core.getUrl('alipay') + '&alipayurl=' + rjson.result.alipay;
				}, false, true);
			});
		});
	};
	modal.initRemonth = function (params) {
		modal.id = params.id;
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			$('#btnSubmit').attr('stop', 1);
			 core.json('car/remonthcard', {id:modal.id}, function(rjson) {
				RhUI.toast.show(rjson.result.message);
				if (rjson.status != 1) {
					$('#btnSubmit').removeAttr('stop');
					return
				}
				setTimeout(function(){
					WeixinJSBridge.call("closeWindow");
				},2000);	
			 },true, true);
		});
	}
	return modal
});