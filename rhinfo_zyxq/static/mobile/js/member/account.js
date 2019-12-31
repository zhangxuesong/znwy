define(['core'], function(core) {
	var modal = {
		endtime:0,
		rid:0,
		isreg:0,
		seconds:60,
		backurl: ''
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
	modal.initBind = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;
		modal.backurl = params.backurl;

		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				temp: '1',
				rid: modal.rid 
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
					return
				}
				else if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
				else if (ret.status == 2){
					$("#verifycode").val(ret.result.message);
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}	
			var carno = $(".car_input").attr("data-pai");
			var ischecked=$('#iscar').is(':checked');
			if(ischecked && carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				}				
			}
			RhUI.confirm('注册即表示同意上述协议',function(){	
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('member/bind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					rid: modal.rid,
					carno:carno
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');
						return
					}
					if (ret.status == 2) {
						RhUI.alert(ret.result.message, '', function() {					
							location.href = ret.result.url;					
						})
						return
					}
					RhUI.alert(ret.result.message, '', function() {					
						location.href = params.backurl ? params.backurl : core.getUrl('home/index');					
					})
				}, true, true);
			});
		})
	};
	modal.initRbind = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;
		modal.backurl = params.backurl;

		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				temp: '1',
				rid: modal.rid 
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
					return
				}
				else if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
				else if (ret.status == 2){
					$("#verifycode").val(ret.result.message);
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			if ($('#pwd').isEmpty()) {
			//	RhUI.toast.show('请输入登录密码');
			//	return
			}
			if ($('#pwd').val() !== $('#pwd1').val()) {
			//	RhUI.toast.show('两次密码输入不一致');
			//	return
			}
			var carno = $(".car_input").attr("data-pai");
			var ischecked=$('#iscar').is(':checked');
			if(ischecked && carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				} 
			}
			RhUI.confirm('注册即表示同意上述协议',function(){
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('member/rbind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					rid: modal.rid,
					pwd: $('#pwd').val(),
					carno:carno
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');
						return
					}				
					RhUI.alert('绑定成功', '', function() {
						location.href = params.backurl ? params.backurl : core.getUrl('home/index')
					})
				}, true, true);
			});
		})
	};	
	modal.initPbind = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;
		modal.backurl = params.backurl;

		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				temp: '2',
				rid: modal.rid 
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
					return
				}
				else if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
				else if (ret.status == 2){
					$("#verifycode").val(ret.result.message);
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			var carno = $(".car_input").attr("data-pai");
			var ischecked=$('#iscar').is(':checked');
			if(ischecked && carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				} 
			}
			RhUI.confirm('注册即表示同意上述协议',function(){
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('member/pbind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					rid: modal.rid,
					carno:carno
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');
						return
					}				
					RhUI.alert('绑定成功', '', function() {
						location.href = params.backurl ? params.backurl : core.getUrl('member')
					})
				}, true, true);
			});
		})
	};
	modal.initPrbind = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;
		modal.backurl = params.backurl;

		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				temp: '2',
				rid: modal.rid 
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
					return
				}
				else if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
				else if (ret.status == 2){
					$("#verifycode").val(ret.result.message);
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			if ($('#pwd').isEmpty()) {
			//	RhUI.toast.show('请输入登录密码');
			//	return
			}
			if ($('#pwd').val() !== $('#pwd1').val()) {
			//	RhUI.toast.show('两次密码输入不一致');
			//	return
			}
			var carno = $(".car_input").attr("data-pai");
			var ischecked=$('#iscar').is(':checked');
			if(ischecked && carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				} 
			}
			RhUI.confirm('注册即表示同意上述协议',function(){
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('member/prbind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					rid: modal.rid,
					pwd: $('#pwd').val(),
					carno:carno
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');
						return
					}				
					RhUI.alert('绑定成功', '', function() {
						location.href = params.backurl ? params.backurl : core.getUrl('member')
					})
				}, true, true);
			});
		})
	};		
	modal.initChgbind = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;

		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				rid: modal.rid,
				temp: 'sms_chgbind'
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
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			$('#btnSubmit').html('正在修改...').attr('stop', 1);
			core.json('member/chgbind', {
				mobile: $('#mobile').val(),
				verifycode: $('#verifycode').val()
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('立即修改').removeAttr('stop');
					return
				}
				RhUI.alert('修改成功', '', function() {
					if(modal.rid !==""){
						location.href = core.getUrl('member') + "&op=bind&rid=" + modal.rid;
					}
					else{
						location.href = core.getUrl('member');
					}
				})
			}, false, true)
		})
	};
	modal.initMybind = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;
		modal.isreg = params.isreg;
		modal.backurl = params.backurl;

		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-type"))
			}
		});
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				temp: '3',
				rid: modal.rid 
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
					return
				}
				else if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
				else if (ret.status == 2){
					$("#verifycode").val(ret.result.message);
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			var myroom = $('#room').val();
			if(myroom==''){
				RhUI.toast.show('请选择房产');
				return
			}
			if ($('#category_type').isEmpty()) {
				RhUI.toast.show('请选择住户类别');
				return
			}
			var category = $('#category_type').val();
			
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			var pwd = '';
			var ownername = '';
			if(modal.isreg==0){
				if ($('#pwd').isEmpty()) {
				//	RhUI.toast.show('请输入登录密码');
				//	return
				}
				if ($('#pwd').val() !== $('#pwd1').val()) {
				//	RhUI.toast.show('两次密码输入不一致');
				//	return
				}
				pwd = $('#pwd').val();
				ownername = $('#ownername').val();
				if(ownername==''){
					RhUI.toast.show('姓名不能为空');
					return
				}
			}
			var carno = $(".car_input").attr("data-pai");			
			var ischecked=$('#iscar').is(':checked');
			if(ischecked && carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				}				
			}
			RhUI.confirm('注册即表示同意上述协议',function(){
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('member/mybind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					rid: modal.rid,
					myroom:myroom,
					otype:category,
					pwd: pwd,
					ownername:ownername,
					carno:carno
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');
						return
					}				
					RhUI.alert(ret.result.message, '', function() {
						location.href = params.backurl ? params.backurl : core.getUrl('member')
					})
				}, true, true);
			});
		})
	};
	modal.initRoomreg = function(params) {		
		$('#btnSubmit').click(function(){			
			var params = [];
			params['building'] = $("#building").val();
			params['unit'] = $("#unit").val();
			params['room'] = $("#room").val();			
			params['floor'] = $("#floor").val();
			if(params['building']==""){
			   RhUI.toast.show('楼宇不能为空');
			   return
			}
			if(params['unit']==""){
			   RhUI.toast.show('单元不能为空');
			   return
			}
			if(params['floor']==""){
			   RhUI.toast.show('楼层不能为空');
			   return
			}
			if(params['room']==""){
			   RhUI.toast.show('房屋不能为空');
			   return
			}
			$("#myform").submit();				
		});
	};
	modal.initMyaudit = function(params) {
		modal.rid = params.rid;
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}			
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			core.json('member/audit', {
				id: $('#memberid').val(),
				reason:'',
				mstatus:0,
				rid:modal.rid
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('审核通过').removeAttr('stop');
					return
				}				
				RhUI.alert('审核完成', '', function() {
					location.href = core.getUrl('manage/member') + '&status=9&rid=' + modal.rid;
				})
			}, true, true)
		});
		$('#btnSubmitno').click(function() {
			if ($('#btnSubmitno').attr('stop')) {
				return
			}			
			$('#btnSubmitno').html('正在提交...').attr('stop', 1);
			core.json('member/audit', {
				id: $('#memberoid').val(),
				reason:$('#reason').val(),
				mstatus:2,
				rid:modal.rid
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('提交').removeAttr('stop');
					return
				}				
				RhUI.alert('审核完成', '', function() {
					location.href = core.getUrl('manage/member') + '&status=9&rid=' + modal.rid;
				})
			}, true, true)
		});
		$('#delSubmit').click(function() {
			if ($('#delSubmit').attr('stop')) {
				return
			}			
			$('#delSubmit').html('正在提交...').attr('stop', 1);
			core.json('manager/audit', {
				id: $('#memberoid').val(),
				mstatus:9,
				rid:modal.rid
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#delSubmit').html('提交').removeAttr('stop');
					return
				}				
				RhUI.alert('操作完成', '', function() {
					location.href = core.getUrl('manage/member') + '&status=9&rid=' + modal.rid;
				})
			}, true, true)
		});
	};
	modal.initBindthird = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;
		modal.backurl = params.backurl;

		var uploadUrl = core.getUrl('util/imageocr');
		
		$(".icon-camera").click(function(){	
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请先输入验证码');
				return
			}			
			var images = {
				localIds: [],
			};
			wx.chooseImage({
				count: 1, // 最多选几张
				sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
				sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
				success: function(res) {
					images.localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
					var i = 0; var length = images.localIds.length;
					var upload = function() {
						wx.uploadImage({
							localId:'' + images.localIds[i],
							isShowProgressTips: 1,
							success: function(res) {
								var serverId = res.serverId;									
								$.ajax({   
									 url:uploadUrl,   
									 type:'post', 
									 data:{
										media_id:serverId,
									 },
									 dataType:'json',
									 success:function(data){ 										
										if (data.error == 1) {
											RhUI.toast.show(data.message);
										} else {											
											$("#bankcard").val(data.bankcard);
										}  
									 }
								});
								//如果还有照片，继续上传
								i++;
								if (i < length) {
									upload();
								}
							}
						});                    
					};
					upload();
				}
			}); 
		});
		
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				temp: '1',
				rid: modal.rid 
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
					return
				}
				else if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
				else if (ret.status == 2){
					$("#verifycode").val(ret.result.message);
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}			
			var bankcard = $('#bankcard').val();
			if(bankcard==''){
				RhUI.toast.show('银行卡号不能为空');
				return
			}
			var realname = $('#ownername').val();
			if(realname==''){
				RhUI.toast.show('姓名不能为空');
				return
			}
			var carno = $(".car_input").attr("data-pai");
			var ischecked=$('#iscar').is(':checked');
			if(ischecked && carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				} 
			}
			RhUI.confirm('注册即表示同意上述协议',function(){
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('member/bind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					rid: modal.rid,
					bankcard: bankcard,
					realname:realname,
					carno:carno
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');					
						return
					}
					RhUI.alert(ret.result.message, '', function() {
						location.href = params.backurl ? params.backurl : core.getUrl('home/index');
					})
				}, true, true);
			});
		})
	};
	modal.initMybindthird = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;
		modal.isreg = params.isreg;
		modal.backurl = params.backurl;

		var uploadUrl = core.getUrl('util/imageocr');
		
		$(".icon-camera").click(function(){	
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请先输入验证码');
				return
			}			
			var images = {
				localIds: [],
			};
			wx.chooseImage({
				count: 1, // 最多选几张
				sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
				sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
				success: function(res) {
					images.localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
					var i = 0; var length = images.localIds.length;
					var upload = function() {
						wx.uploadImage({
							localId:'' + images.localIds[i],
							isShowProgressTips: 1,
							success: function(res) {
								var serverId = res.serverId;									
								$.ajax({   
									 url:uploadUrl,   
									 type:'post', 
									 data:{
										media_id:serverId,
									 },
									 dataType:'json',
									 success:function(data){ 										
										if (data.error == 1) {
											RhUI.toast.show(data.message);
										} else {											
											$("#bankcard").val(data.bankcard);
										}  
									 }
								});
								//如果还有照片，继续上传
								i++;
								if (i < length) {
									upload();
								}
							}
						});                    
					};
					upload();
				}
			}); 
		});
		
		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-type"))
			}
		});
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				temp: '3',
				rid: modal.rid 
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
					return
				}
				else if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
				else if (ret.status == 2){
					$("#verifycode").val(ret.result.message);
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			var myroom = $('#room').val();
			if(myroom==''){
				RhUI.toast.show('请选择房产');
				return
			}
			if ($('#category_type').isEmpty()) {
				RhUI.toast.show('请选择住户类别');
				return
			}
			var category = $('#category_type').val();
			
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			var bankcard = $('#bankcard').val();			
			if(bankcard==''){
				RhUI.toast.show('银行卡号不能为空');
				return
			}
			var pwd = '';
			var ownername = '';
			if(modal.isreg==0){
				if ($('#pwd').isEmpty()) {
				//	RhUI.toast.show('请输入登录密码');
				//	return
				}
				if ($('#pwd').val() !== $('#pwd1').val()) {
				//	RhUI.toast.show('两次密码输入不一致');
				//	return
				}
				pwd = $('#pwd').val();
				ownername = $('#ownername').val();
				if(ownername==''){
					RhUI.toast.show('姓名不能为空');
					return
				}
			}
			var carno = $(".car_input").attr("data-pai");			
			var ischecked=$('#iscar').is(':checked');
			if(ischecked && carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				}				
			}
			RhUI.confirm('注册即表示同意上述协议',function(){
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('member/mybind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					rid: modal.rid,
					myroom:myroom,
					otype:category,
					pwd: pwd,
					ownername:ownername,
					bankcard: bankcard,
					carno:carno
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');
						return
					}				
					RhUI.alert(ret.result.message, '', function() {
						location.href = params.backurl ? params.backurl : core.getUrl('member/myhouse')
					})
				}, true, true);
			});
		})
	};
	modal.initBinddoor = function(params) {
		modal.endtime = params.endtime;
		modal.rid = params.rid;
		modal.backurl = params.backurl;
	
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/verifycode', {
				mobile: $('#mobile').val(),
				temp: '1',
				rid: modal.rid 
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
					return
				}
				else if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
				else if (ret.status == 2){
					$("#verifycode").val(ret.result.message);
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}					
			var realname = $('#ownername').val();
			if(realname==''){
				RhUI.toast.show('姓名不能为空');
				return
			}
			if (!$('#idcard').isIDCard()) {
				RhUI.toast.show('请输入正确身份证号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}		
			var idcard = $('#idcard').val();
			var carno = $(".car_input").attr("data-pai");
			var ischecked=$('#iscar').is(':checked');
			if(ischecked && carno !='' && typeof(carno) !='undefined'){
				var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
				if(carno.search(re) == -1) {
					RhUI.toast.show('车牌输入不正确');
					return 
				} 
			}
			RhUI.confirm('注册即表示同意上述协议',function(){
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('member/bind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					rid: modal.rid,
					idcard:idcard,
					realname:realname,
					carno:carno
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');					
						return
					}
					RhUI.alert(ret.result.message, '', function() {
						location.href = params.backurl ? params.backurl : core.getUrl('home/index');
					})
				}, true, true);
			});
		})
	};
	return modal
});