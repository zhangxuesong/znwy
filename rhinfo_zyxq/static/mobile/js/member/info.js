define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		params = $.extend({
			returnurl: '',
			template_flag: 0
		}, params || {});
		require(['rhui.picker'], function() {
			$('#city').cityPicker({
				showArea: false
			});
			$('#birthday').datePicker()
		});
		$('#btn-submit').click(function() {
			var postdata = {};
			if (params.template_flag == 0) {
				if ($('#realname').isEmpty()) {
					RhUI.toast.show('请填写姓名');
					return
				}
				if (!$('#mobile').isMobile() && !params.wapopen) {
					RhUI.toast.show('请填写正确手机号码');
					return
				}
				if ($(this).attr('submit')) {
					return
				}
				var birthday = $('#birthday').val().split('-');
				var citys = $('#city').val().split(' ');
				$(this).html('处理中...').attr('submit', 1);
				postdata = {
					'memberdata': {
						'realname': $('#realname').val(),
						'weixin': $('#weixin').val(),
						'gender': $('#sex').val(),
						'birthyear': $('#birthday').val().length > 0 ? birthday[0] : 0,
						'birthmonth': $('#birthday').val().length > 0 ? birthday[1] : 0,
						'birthday': $('#birthday').val().length > 0 ? birthday[2] : 0,
						'province': $('#city').val().length > 0 ? citys[0] : '',
						'city': $('#city').val().length > 0 ? citys[1] : ''
					},
					'mcdata': {
						'realname': $('#realname').val(),
						'gender': $('#sex').val(),
						'birthyear': $('#birthday').val().length > 0 ? birthday[0] : 0,
						'birthmonth': $('#birthday').val().length > 0 ? birthday[1] : 0,
						'birthday': $('#birthday').val().length > 0 ? birthday[2] : 0,
						'resideprovince': $('#city').val().length > 0 ? citys[0] : '',
						'residecity': $('#city').val().length > 0 ? citys[1] : ''
					}
				};
				if (!params.wapopen) {
					postdata.memberdata.mobile = $('#mobile').val();
					postdata.mcdata.mobile = $('#mobile').val()
				}
				core.json('member/info/submit', postdata, function(json) {
					modal.complete(params, json)
				}, true, true)
			} else {
				RhUI.loader.show('mini');
				$(this).html('处理中...').attr('submit', 1);
				require(['biz/plugin/diyform'], function(diyform) {
					postdata = diyform.getData('.diyform-container');
					RhUI.loader.hide();
					if (postdata) {
						core.json('member/info/submit', {
							memberdata: postdata
						}, function(json) {
							modal.complete(params, json)
						}, true, true)
					} else {
						$('#btn-submit').html('确认修改').removeAttr('submit')
					}
				})
			}
		})
	};
	modal.complete = function(params, json) {
		RhUI.loader.hide();
		if (json.status == 1) {
			RhUI.toast.show('保存成功');
			if (params.returnurl) {
				location.href = params.returnurl
			} else {
				history.back()
			}
		} else {
			$('#btn-submit').html('确认修改').removeAttr('submit');
			RhUI.toast.showshow('保存失败')
		}
	};
	return modal
});