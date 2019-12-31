define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		status: 1,
		bid:0,
	};
	modal.init = function(params) {
		modal.bid = params.bid;

		$('#btnFollow').click(function() {
			core.json('business/follow', {
				id: modal.bid
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				var isfollow = ret.result.isfollow;
				var follows = parseInt($('#myfollows').html());
				if (isfollow) {
					$('#btnFollow').html('<i class="icon icon-check"></i> 已关注').removeClass('btn-warning').addClass('btn-default');					
					$('#myfollows').html(follows + 1);
				} else {
					$('#btnFollow').html('<i class="icon icon-add"></i> 关注').removeClass('btn-default').addClass('btn-warning');
					$('#myfollows').html(follows - 1);
				}
				$('#btnFollow').removeAttr('stop');
			}, true, true)
		});
		$('.fui-content').infinite({
            onLoading: function () {
				if(modal.status==1){
					modal.getList1();
				}
				if(modal.status==2){
					modal.getList2();
				}
				if(modal.status==3){
					modal.getList3();
				}
            }
        });
        if (modal.page == 1) {
          	if(modal.status==1){
				modal.getList1();
			}
			if(modal.status==2){
				modal.getList2();
			}
			if(modal.status==3){
				modal.getList3();
			}
        }
		
		RhUI.tab({
			container: $('#tab'),
			handlers: {			
				tab1: function() {
					modal.changeTab(1)
				},
				tab2: function() {
					modal.changeTab(2)
				},
				tab3: function() {
					modal.changeTab(3)
				}
			}
		});
	};
	
	modal.getList1 = function() {	
		$('.content-empty').hide();		
		core.html('business/intro', {
			id: modal.bid
		}, function(html) {
			if (html=='') {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} 
			else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				$('.container').html(html);
				$('.fui-content').infinite('stop')
			}		
		})
	};
	modal.getList2 = function() {
		$('.content-empty').hide();		
		core.json('business/activity', {
			page: modal.page,
			id: modal.bid
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
			} 
			else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_activity_list', result, modal.page > 1);			
		})
	};
	modal.getList3 = function() {
		$('.content-empty').hide();
		core.json('business/comment', {
			page: modal.page,
			id: modal.bid
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
			core.tpl('.container', 'tpl_comment_list', result, modal.page > 1);			
		})
	};
	modal.changeTab = function(status) {
		$('.content-empty').hide();
		$('.container').html('');
		$('.fui-content-inner').infinite('init');
		$('.infinite-loading').show();
		modal.page = 1;
		modal.status = status;	
		if(modal.status==1){
			modal.getList1();
		}
		if(modal.status==2){
			modal.getList2();
		}
		if(modal.status==3){
			modal.getList3();
		}
	};
	return modal
});