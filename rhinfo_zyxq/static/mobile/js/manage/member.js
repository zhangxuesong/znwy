define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		rid: 0,
		room:'',
		status:''
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
		core.json('manage/member', {
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
	modal.initMyaudit = function(params) {
		modal.rid = params.rid;		
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getMyaudit()
            }
        });
				
        if (modal.page == 1) {
            modal.getMyaudit()
        }		
	};
	modal.getMyaudit = function() {		
		core.json('manage/mymember', {
			page: modal.page,
			rid: modal.rid			
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
	modal.initSelectroom = function() {		
		$('#btnSubmit').click(function(){			
			var selectroom = $("#room").val();
			if(selectroom==''){
			   RhUI.toast.show('房产不能为空');
			   return
			}
			$("#myform").submit();			
		});
	};
	modal.initEnterroom = function() {		
		$("#btn-submit").unbind('click').click(function () {
            if (modal.stop) {
                return
            }
			var rid = $.trim($("#rid").val());
			var otype = $.trim($("#otype").val());
			var room = $.trim($("#room").val());
            var ownername = $.trim($("#ownername").val());
            var mobile = $.trim($("#mobile").val());
            var mobile1 = $.trim($("#mobile1").val());
			var isnotice = 0;
			var ischecked=$('#isnotice').is(':checked');
			if(ischecked) isnotice = 1;
						
            if (ownername == '') {
                RhUI.toast.show("请输入真实姓名");
                return
            }
            if (mobile == '') {
                RhUI.toast.show("请输入手机号");
                return
            }
           
            modal.stop = true;
            var obj = {rid:rid,otype:otype,room:room,ownername:ownername,mobile: mobile,mobile1:mobile1,isnotice:isnotice};
            core.json('manage/enterroom', obj, function (json) {
                if (json.status == 1) {
                    RhUI.toast.show("保存成功");
                    location.href = core.getUrl('manage/index');
                    return
                } else {
                    RhUI.toast.show(json.result.message)
                }
                modal.stop = false
            }, true, true)
        });       
	};
	modal.initChangeroom = function() {		
		$("#btn-submit").unbind('click').click(function () {
            if (modal.stop) {
                return
            }
			var rid = $.trim($("#rid").val());		
			var room = $.trim($("#room").val());
            var ownername = $.trim($("#ownername").val());
            var mobile = $.trim($("#mobile").val());
									
            if (ownername == '') {
                RhUI.toast.show("请输入真实姓名");
                return
            }
            if (mobile == '') {
                RhUI.toast.show("请输入手机号");
                return
            }
           
            modal.stop = true;
            var obj = {rid:rid,room:room,ownername:ownername,mobile: mobile};
            core.json('manage/changeroom', obj, function (json) {
                if (json.status == 1) {
                    RhUI.toast.show("保存成功");
                    location.href = core.getUrl('manage/index');
                    return
                } else {
                    RhUI.toast.show(json.result.message)
                }
                modal.stop = false
            }, true, true)
        });       
	};
	modal.initAbnroom = function() {		
		$("#btn-submit").unbind('click').click(function () {
            if (modal.stop) {
                return
            }
			var rid = $.trim($("#rid").val());		
			var room = $.trim($("#room").val());
            var content = $.trim($("#content").val());
            var remark = $.trim($("#remark").val());
			var isfree =  $('input[name="isfree"]:checked').val(); 
									
            if (content == '') {
                RhUI.toast.show("请输入异常描述");
                return
            }
                     
            modal.stop = true;
            var obj = {rid:rid,room:room,content:content,remark:remark,isfree:isfree};
            core.json('manage/abnroom', obj, function (json) {
                if (json.status == 1) {
                    RhUI.toast.show("保存成功");
                    location.href = core.getUrl('manage/index');
                    return
                } else {
                    RhUI.toast.show(json.result.message)
                }
                modal.stop = false
            }, true, true)
        });       
	};
	
	return modal
});