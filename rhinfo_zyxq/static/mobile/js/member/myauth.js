define(['core'], function(core) {
	var modal = {
		endtime:0,
		isverify:0,
		seconds:60
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

	modal.init = function(params) {
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
			core.json('myauth/verifycode', {
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
		$("#verifylogin").on('click',function () {
			var ischecked=$('#verifylogin').is(':checked');
			if(ischecked){
				$('#verify').show();		
				$('#pwd').hide();
				modal.isverify = 1;
			}
			else{
				$('#verify').hide();		
				$('#pwd').show();
				modal.isverify = 0;
			}
		});
		$('#mobile').bind('input propertychange', function() {
			var mobile_value = $(this).val();
			if (mobile_value.length == '11') {
				core.json('myauth/mobile_exist', {
					mobile: mobile_value					
				}, function(ret) {
					if (ret.status == 1) {
						RhUI.toast.show(ret.result.message);						
						return
					}									
				}, false, true)			
			} 
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位正确手机号码');
				return
			}
			
			if(modal.isverify==1){
				if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
					RhUI.toast.show('请输入5位数字验证码');
					return
				}
			}
			else{
				if ($('#password').isEmpty()) {
					RhUI.toast.show('请输入登录密码');
					return
				}
			}
			$('#btnSubmit').html('正在登录...').attr('stop', 1);
			core.json('myauth/login', {
				username: $('#mobile').val(),
				verifycode: $('#verifycode').val(),
				password: $('#password').val(),
				isverify:modal.isverify
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('登录').removeAttr('stop');
					return
				}				
				location.href = core.getUrl('home/index');
			}, true, true)
		})
	};	
	modal.initReg = function(params) {
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
			core.json('myauth/verifycode', {
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
		$('#mobile').bind('input propertychange', function() {
			var mobile_value = $(this).val();
			if (mobile_value.length == '11') {
				core.json('myauth/mobile_exist', {
					mobile: mobile_value					
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);						
						return
					}									
				}, false, true)			
			} 
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
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
			if ($('#pwd').isEmpty()) {
				RhUI.toast.show('请输入密码');
				return
			}

			if($('#pwd').len()< 6) {
				RhUI.toast.show('密码不能小于六位数');
				return;
			}
			if ($('#pwd').val() !== $('#pwd1').val()) {
				RhUI.toast.show('两次密码输入不一致');
				return
			}
			
			$('#btnSubmit').html('正在注册...').attr('stop', 1);
			core.json('myauth/register', {
				mobile: $('#mobile').val(),
				verifycode: $('#verifycode').val(),
				pwd: $('#pwd').val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('立即注册').removeAttr('stop');
					return
				}				
				location.href = core.getUrl('home/index')				
			}, true, true)
		})
	};
	modal.initFor = function(params) {
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
			core.json('myauth/verifycode', {
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
		$('#mobile').bind('input propertychange', function() {
			var mobile_value = $(this).val();
			if (mobile_value.length == '11') {
				core.json('myauth/mobile_exist', {
					mobile: mobile_value					
				}, function(ret) {
					if (ret.status == 1) {
						RhUI.toast.show('手机号码不存在');						
						return
					}									
				}, false, true)			
			} 
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
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
			if ($('#pwd').isEmpty()) {
				RhUI.toast.show('请输入密码');
				return
			}
			if($('#pwd').len()< 6) {
				RhUI.toast.show('密码不能小于六位数');
				return;
			}
			if ($('#pwd').val() !== $('#pwd1').val()) {
				RhUI.toast.show('两次密码输入不一致');
				return
			}
			
			$('#btnSubmit').html('正在找回...').attr('stop', 1);
			core.json('myauth/forget', {
				mobile: $('#mobile').val(),
				verifycode: $('#verifycode').val(),
				pwd: $('#pwd').val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('立即找回').removeAttr('stop');
					return
				}				
				location.href = core.getUrl('auth')				
			}, true, true)
		})
	};
	modal.initModify = function(params) {
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
			core.json('myauth/verifycode', {
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

		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
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
			if ($('#pwd').isEmpty()) {
				RhUI.toast.show('请输入密码');
				return
			}
			if($('#pwd').len()< 6) {
				RhUI.toast.show('密码不能小于六位数');
				return;
			}
			if ($('#pwd').val() !== $('#pwd1').val()) {
				RhUI.toast.show('两次密码输入不一致');
				return
			}
			
			$('#btnSubmit').html('正在修改...').attr('stop', 1);
			core.json('myauth/modify', {
				mobile: $('#mobile').val(),
				verifycode: $('#verifycode').val(),
				pwd: $('#pwd').val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('提交').removeAttr('stop');
					return
				}				
				location.href = core.getUrl('auth')				
			}, true, true)
		})
	};		
	return modal
});