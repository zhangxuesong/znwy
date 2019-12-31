define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		status: '',
		rid: 0
	};
	modal.init = function(params) {
		modal.status = params.status;
		modal.rid = params.rid;
		if (modal.page==1) {
			modal.getList();			
		}
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList()
			}
		});
				

		RhUI.tab({
			container: $('#tab'),
			handlers: {
				tab: function() {
					modal.changeTab('')
				},
				tab0: function() {
					modal.changeTab(1)
				},
				tab1: function() {
					modal.changeTab(2)
				},
				tab2: function() {
					modal.changeTab(3)
				},
				tab3: function() {
					modal.changeTab(8)
				}				
			}
		});
	};
	modal.changeTab = function(status) {
		$('.fui-content').infinite('init');
		$('.content-empty').hide();
		$('.container').html('');
		$('.infinite-loading').show();
		modal.page = 1;
		modal.status = status;		
		modal.getList();
	};
	modal.getList = function() {
		core.json('service/mysuggest', {
			page: modal.page,
			status: modal.status,
			rid: modal.rid
		}, function(ret) {
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
			core.tpl('.container', 'tpl_suggest_index_list', result, modal.page > 1);
		})
	};
	return modal
});