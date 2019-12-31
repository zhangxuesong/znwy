define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		pid: 0,
		rid: 0,
		status: 1,
		keyword:'',
		paydate:''
	};
	modal.init = function(params) {
		modal.rid = params.rid;
		modal.status = params.status;
		modal.keyword = params.keyword;	
	    core.json('manager/get_parking', {rid:modal.rid}, function (json) {
            if (json.status == 1) {
                $("#parking0").text(json.result.parking0);
                $("#parking1").text(json.result.parking1);
                $("#parking2").text(json.result.parking2);
            }
        });
		$('.fui-content').infinite({
            onLoading: function () {
				modal.getParking();				
            }
        });
        if (modal.page == 1) {
			modal.getParking();			
        }
		
		RhUI.tab({
			container: $('#tab'),
			handlers: {			
				tab0: function() {
					modal.switchTab(0)
				},
				tab1: function() {
					modal.switchTab(1)
				},
				tab2: function() {
					modal.switchTab(2)
				},
				tab3: function() {
					modal.switchTab(3)
				}
			}
		});
	};
	modal.initList = function(params) {
		modal.rid = params.rid;
		modal.status = params.status;
		modal.paydate = params.paydate;	
	    core.json('manager/get_carpay', {rid:modal.rid}, function (json) {
            if (json.status == 1) {
                $("#today_carpay").text(json.result.today_carpay);
                $("#yestoday_carpay").text(json.result.yestoday_carpay);
                $("#month_carpay").text(json.result.month_carpay);
            }
        });
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#paydate').datePicker()
		});
		
		$('.fui-content').infinite({
            onLoading: function () {
				modal.getList();				
            }
        });
        if (modal.page == 1) {
			modal.getList();			
        }
		
		RhUI.tab({
			container: $('#tab'),
			handlers: {			
				tab0: function() {
					modal.changeTab(0)
				},
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
	modal.getList = function() {		
		core.json('manager/carpay', {
			page: modal.page,
			rid: modal.rid,
			status: modal.status,
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
			core.tpl('.container', 'tpl_carpay_list', result, modal.page > 1);			
		})
	};
	modal.getParking = function() {		
		core.json('manager/parking', {
			page: modal.page,
			rid: modal.rid,
			status: modal.status,
			keyword: modal.keyword
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
			core.tpl('.container', 'tpl_parking_list', result, modal.page > 1);			
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
	modal.switchTab = function(status) {
		$('.fui-content-inner').infinite('init');
		$('.content-empty').hide();
		$('.container').html('');
		$('.infinite-loading').show();
		modal.page = 1;
		modal.status = status;
		$("#status").val(status);
		modal.getParking();		
	};
	return modal
});