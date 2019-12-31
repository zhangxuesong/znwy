define(['core','tpl'], function(core, tpl) {
	var modal = {
		op:'',
		page: 1
	};
	modal.init = function(params) {
		modal.op = params.op;
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }
	};

	modal.getList = function() {		
		core.json('service/' + modal.op, {
			page: modal.page
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
			core.tpl('.container', 'tpl_creditlog_list', result, modal.page > 1);			
		})
	};
	return modal
});