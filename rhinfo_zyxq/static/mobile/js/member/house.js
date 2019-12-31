define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		id:0,
		backurl: '',
		seconds:60
	};
	modal.initList = function() {
		$('*[data-toggle=delete]').unbind('click').click(function() {
			var item = $(this).closest('.address-item');
			var id = item.data('addressid');
			RhUI.confirm('删除后无法恢复, 确认要删除吗 ?', function() {
				core.json('member/delete', {
					id: id
				}, function(ret) {
					if (ret.status == 1) {
						if (ret.result.defaultid) {
							$("[data-addressid='" + ret.result.defaultid + "']").find(':radio').prop('checked', true)
						}
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
		$(document).on('click', '[data-toggle=setdefault]', function() {
			var item = $(this).closest('.address-item');
			var id = item.data('addressid');
			core.json('member/setdefault', {
				id: id
			}, function(ret) {
				if (ret.status == 1) {
					$('.fui-content').prepend(item);
					RhUI.toast.show("设置默认成功");
					return
				}
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.initShare = function(params) {
		modal.endtime = params.endtime;
        modal.id = params.id;
		modal.rid = params.rid;
		modal.backurl = params.backurl;

		$('#btn-share').click(function() {
				$('#cover').fadeIn(200)
			});
		$('#cover').click(function() {
			$('#cover').hide()
		})
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('member/shareverifycode', {
				mobile: $('#mobile').val(),
				temp: 'sms_bind',
				rid: modal.rid
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
			if ($('#realname').isEmpty()) {
				RhUI.toast.show('请输入您的姓名');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}		
			$('#btnSubmit').html('正在绑定...').attr('stop', 1);
			core.json('member/gethouse', {
				mobile: $('#mobile').val(),
				realname: $('#realname').val(),
				verifycode: $('#verifycode').val(),
				id: modal.id
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('立即绑定').removeAttr('stop');
					return
				}
				RhUI.alert('绑定成功', '', function() {
					location.href = params.backurl ? params.backurl:core.getUrl('member/myhouse')
				})
			}, true, true)
		})		
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
	modal.initOtype = function() {
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#effedate').datePicker()
		});	
		$('#btnSubmit').click(function(){			
			var params = [];
			params['id'] = $("#memberid").val();
			params['effedate'] = $("#effedate").val();
			var otype = $("input[name=otype]:checked").val();
			if(otype==2){
				if(params['effedate']==""){
				   RhUI.toast.show('有效日期不能为空');
				   return
				}
			}
			$("#myform").submit();			
		});
	};
	return modal
});