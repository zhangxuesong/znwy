define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		rid: 0,
		room:'',
		status:'',
		paydate:''
	};
	
	modal.initPayfee = function(params) {
		modal.rid = params.rid;
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
		core.json('manage/payfee', {
			page: modal.page,
			rid: modal.rid,
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
	
	modal.initList = function(params) {
		modal.rid = params.rid;
		modal.room = params.room;
		modal.status = params.status;
		
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
				
        if (modal.page == 1) {
            modal.getList()
        }
		RhUI.tab({
			container: $('#tab'),
			handlers: {			
				tab1: function() {
					modal.changeTab(0)
				},
				tab2: function() {
					modal.changeTab(1)
				},
				tab3: function() {
					modal.changeTab(2)
				}
			}
		});
	};
	
	modal.getList = function() {		
		core.json('manage/fee', {
			page: modal.page,
			rid: modal.rid,
			room: modal.room,
			status: modal.status
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
	
	modal.changeTab = function(status) {
		$('.fui-content-inner').infinite('init');
		$('.content-empty').hide();
		$('.container').html('');
		$('.infinite-loading').show();
		modal.page = 1;
		modal.status = status;	
		$("#status").val(status);
		modal.getList();
	};	
	return modal
});