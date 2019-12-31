define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		sid: 0,
		paydate:''
	};
	
	modal.init = function(params) {
		modal.sid = params.sid;
		modal.paydate = params.paydate;		
		
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#paydate').datePicker()
		});	
		
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getPayfee()
            }
        });
				
        if (modal.page == 1) {
            modal.getPayfee()
        }
	};
	modal.getPayfee = function() {		
		core.json('express/payfee', {
			page: modal.page,
			sid: modal.sid,
			paydate: modal.paydate
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
			core.tpl('.container', 'tpl_feebill_list', result, modal.page > 1);			
		})
	};
	return modal
});