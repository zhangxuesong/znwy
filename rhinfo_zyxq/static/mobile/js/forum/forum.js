define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		keyword: '',
		rid: 0
	};
	
	modal.init = function(params) {
		modal.rid = params.rid ? params.rid : 0;	
		modal.keyword = params.keyword ? params.keyword : '' ;	
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getBoardList()
			}
		});
		
		if (modal.page == 1) {
			modal.getBoardList()			
		}

	};
	modal.getBoardList = function() {
		core.json('forum/boardlist', {
			page: modal.page,
			keyword: modal.keyword
		}, function(ret) {				
			var result = ret.result;			
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			
			modal.page++;
			core.tpl('.mycontainer', 'tpl_board_lists', result, modal.page > 1)
		})
	};

	return modal
});