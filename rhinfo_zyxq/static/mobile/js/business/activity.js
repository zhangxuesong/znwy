define(['core', 'tpl'], function(core, tpl) {
	var modal = {page: 1,bid: 0,signtime:''};
	modal.init = function(params) {
		modal.params = params;		
		$('#btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}			
			if ($('#realname').isEmpty()) {
				RhUI.toast.show('姓名不能为空!');
				return
			}
			if ($('#mobile').isEmpty()) {
				RhUI.toast.show('手机号码不能为空!');
				return
			}
			
			var realname = $('#realname').val();
			var mobile =$('#mobile').val();	
			var msg = modal.params.msg?modal.params.msg:'报名成功!';			
			$(this).html('正在处理...').attr('stop', 1);
			core.json('business/actdetail', {
				'id': modal.params.id,
				'realname': realname,
				'mobile': mobile
			}, function(ret) {
				if (ret.status == 1) {
					RhUI.alert(msg, '', function() {
						location.href = modal.params.url?modal.params.url:core.getUrl('home/index');						
					});
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.initSignup = function(params) {
		modal.bid = params.bid;
		modal.signtime = params.signtime;
		
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#signtime').datePicker()
		});	
		
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getSignup()
            }
        });
				
        if (modal.page == 1) {
            modal.getSignup()
        }
	};
	
	modal.getSignup = function() {		
		core.json('business/signup', {
			page: modal.page,
			bid: modal.bid,
			signtime: modal.signtime
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
			core.tpl('.container', 'tpl_signup_list', result, modal.page > 1);			
		})
	};
	return modal
});