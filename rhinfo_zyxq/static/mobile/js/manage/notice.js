define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		rid: 0,
		page: 1
	};
	modal.init = function (params) {
	   modal.rid = params.rid;
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
		core.json('manage/mynotice', {
			page: modal.page,rid:modal.rid
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
			core.tpl('.container', 'tpl_notice_list', result, modal.page > 1);			
		})
	};
	return modal
});