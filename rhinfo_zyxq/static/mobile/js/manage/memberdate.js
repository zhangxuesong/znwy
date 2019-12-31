define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		rid: 0,
		binddate:'',
		status:''
	};
	modal.init = function(params) {
		modal.rid = params.rid;
		modal.binddate = params.binddate;
		modal.status = params.status;
		
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#binddate').datePicker()
		});	
		
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
				tab0: function() {
					modal.changeTab(9)
				},
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
		core.json('manage/memberdate', {
			page: modal.page,
			rid: modal.rid,
			binddate: modal.binddate,
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
			core.tpl('.container', 'tpl_member_list', result, modal.page > 1);			
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