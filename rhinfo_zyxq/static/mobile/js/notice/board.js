define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		pid: 0,
		rid: 0,
		bid: 0,
		tid: 0
	};
	modal.init = function(params) {
		modal.pid = params.pid;
		modal.rid = params.rid;
		modal.bid = params.bid;
		modal.tid = params.tid;

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
		core.json('notice/index', {
			page: modal.page,
			pid: modal.pid,
			rid: modal.rid,
			bid: modal.bid,
			tid: modal.tid
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
	modal.initDetail = function(params) {
		var noticeid = params.id;		
		$('#btnSend').click(function() {
			core.json('notice/status', {
				id:noticeid
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				RhUI.alert(ret.result.message, '', function() {
					location.href = core.getUrl('manage/notice');
				})				
			}, true, true)
		});
	};
	return modal
});