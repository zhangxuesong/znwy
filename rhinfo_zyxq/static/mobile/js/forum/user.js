define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		page: 1
	};
	modal.init = function(params) {
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
		core.json('forum/mypost', {
			page: modal.page
		}, function(ret) {
			var result = ret.result;
			if (result.total <= 0) {
				$('#user-posts-list').hide();
				$('.empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('#user-posts-list').show();
				$('.empty').hide();
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
		core.json('forum/myreply', {
			page: modal.page
		}, function(ret) {
			var result = ret.result;
			if (result.total <= 0) {
				$('#user-posts-list').hide();
				$('.empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('#user-posts-list').show();
				$('.empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('#user-replys-list', 'tpl_user_reply_list', result, modal.page > 1);
			modal.bindEvents()
		})
	};
	modal.bindEvents = function() {
		$('.delete-link').click(function(e) {
			e.stopPropagation();
			e.stopImmediatePropagation();
			var obj = $(this);
			var item = obj.closest('.fui-list');
			var pid = item.data('postid');
			RhUI.confirm('确认要删除此评论?', function() {
				core.json('forum/delmycomm', {
					id: pid
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						return
					}
					item.prev('.fui-list').remove();
					item.next('.fui-list').remove();
					item.remove();
					RhUI.toast.show('删除成功!')
				})
			})
		})
	};
	return modal
});