define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		page: 1,rid:0
	};
	modal.init = function(params) {
		modal.rid = params.rid;
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList()
			}
		});
		if (modal.page == 1) {
			modal.getList()			
		}
	};
	modal.getList = function() {
		core.json('manage/mypost', {
			page: modal.page,rid:modal.rid
		}, function(ret) {
			var result = ret.result;
			if (result.total <= 0) {
				$('#user-posts-list').hide();
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('#user-posts-list').show();
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('#user-posts-list', 'tpl_user_posts_list', result, modal.page > 1)
		})
	};
	modal.initReply = function(params) {
		modal.rid = params.rid;
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getRlist()
			}
		});
		if (modal.page == 1) {
			modal.getRlist()		
		}
	};
	modal.getRlist = function() {
		core.json('manage/myreply', {
			page: modal.page,rid:modal.rid
		}, function(ret) {
			var result = ret.result;
			if (result.total <= 0) {
				$('#user-posts-list').hide();
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('#user-posts-list').show();
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('#user-replys-list', 'tpl_user_reply_list', result, modal.page > 1);
		})
	};
	return modal
});