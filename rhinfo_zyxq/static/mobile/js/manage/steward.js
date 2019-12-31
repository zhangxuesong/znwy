define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		pid: 0,
		rid: 0,
		bid: 0,
		tid: 0,
		status: 1,
		keyword:'',
		ismanage:1
	};
	modal.init = function(params) {
		modal.pid = params.pid;
		modal.rid = params.rid;
		modal.bid = params.bid;
		modal.tid = params.tid;
		modal.status = params.status;
		modal.ismanage = 0;
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
	modal.initSteward = function(params) {
		modal.rid = params.rid;
		modal.status = params.status;
		modal.keyword = params.keyword;
		
		$('.fui-content').infinite({
            onLoading: function () {
				if(modal.status==1){
					modal.getList2();
				}
				if(modal.status==2){
					modal.getList3();
				}
				if(modal.status==3){
					modal.getList4();
				}
				if(modal.status==4){
					modal.getList5();
				}
            }
        });
        if (modal.page == 1) {
          	if(modal.status==1){
				modal.getList2();
			}
			if(modal.status==2){
				modal.getList3();
			}
			if(modal.status==3){			 
				modal.getList4();
			}
			if(modal.status==4){			 
				modal.getList5();
			}
        }
		
		RhUI.tab({
			container: $('#tab'),
			handlers: {			
				tab1: function() {
					modal.changeTab1(1)
				},
				tab2: function() {
					modal.changeTab1(2)
				},
				tab3: function() {
					modal.changeTab1(3)
				},
				tab4: function() {
					modal.changeTab1(4)
				}
			}
		});
	};
	modal.getList1 = function() {		
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
	modal.getList2 = function() {		
		core.json('service/repair', {
			page: modal.page,
			pid: modal.pid,
			rid: modal.rid,
			keyword:modal.keyword,
			ismanage:modal.ismanage
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop');				
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_repair_list', result, modal.page > 1);			
		})
	};
	modal.getList3 = function() {		
		core.json('service/suggest', {
			page: modal.page,
			pid: modal.pid,
			rid: modal.rid,
			keyword:modal.keyword,
			ismanage:modal.ismanage
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop');					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_suggest_list', result, modal.page > 1);			
		})
	};
	modal.getList4 = function() {		
		core.json('manage/door', {
			page: modal.page,
			pid: modal.pid,
			rid: modal.rid,
			keyword:modal.keyword
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop');					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_door_list', result, modal.page > 1);
			modal.bindEvents();
		});		
	};
	modal.getList5 = function() {		
		core.json('manager/repairp', {
			page: modal.page,
			pid: modal.pid,
			rid: modal.rid,
			keyword:modal.keyword,
			ismanage:modal.ismanage
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop');				
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_repairp_list', result, modal.page > 1);			
		})
	};
	modal.bindEvents = function() {
		$('.fui-card-btns .opendoor').unbind('click').click(function() {
			var self = $(this);
			var lockid = self.attr("id"); 
			var online = self.data("online");			
			if(online==2){
			   RhUI.toast.show('设备不在线');
			   return
			}
			self.find('span').html('<i class="icon icon-yaochi text-success"></i>'); 
			core.json('manage/opendoor', {
				id: lockid,rid:modal.rid			
			}, function(ret) {
				if (ret.status != 1) {
					self.find('.span').html('<i class="icon icon-warn"></i>');
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.find('.span').html('<i class="icon icon-lock text-success"></i>'); },2000);
				}
				self.find('span').html('<i class="icon icon-unlock text-success"></i>'); 
				RhUI.toast.show('开门成功');
				setTimeout(function(){self.find('span').html('<i class="icon icon-lock text-success"></i>'); },2000);
			}, false, true)
        });
	};
	modal.changeTab = function(status) {
		$('.fui-content-inner').infinite('init');
		$('.content-empty').hide();
		$('.container').html('');
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
	modal.changeTab1 = function(status) {
		$('.fui-content-inner').infinite('init');
		$('.content-empty').hide();
		$('.container').html('');
		$('.infinite-loading').show();
		modal.page = 1;
		modal.status = status;		
		$("#status").val(status);
		modal.keyword = '';
		$("#keyword").val('');
		if(modal.status==1){
			modal.getList2();
		}
		if(modal.status==2){
			modal.getList3();
		}
		if(modal.status==3){
			modal.getList4();
		}
		if(modal.status==4){
			modal.getList5();
		}
	};
	return modal
});