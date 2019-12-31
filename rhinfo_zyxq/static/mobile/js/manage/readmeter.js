define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		rid: 0,
		room:''
	};
	modal.init = function(params) {		
		$('#btnSubmit').click(function(){			
			var selectroom = $("#room").val();
			if(selectroom==''){
			   RhUI.toast.show('房产不能为空');
			   return
			}
			$("#myform").submit();			
		});
	};
	modal.initRead = function() {				
		$("#btn-submit").unbind('click').click(function () {
            if (modal.stop) {
                return
            }
			var rid = $.trim($("#rid").val());			
			var room = $.trim($("#room").val());
			var startdate = $.trim($("#startdate").val());
			var enddate = $.trim($("#enddate").val());           
			var endqty = $.trim($("#endqty").val());
			var feeitem = $("#feeitem").find("option:selected").val(); 
           
            if (startdate == '') {
                RhUI.toast.show("请输入起始月份");
                return
            }
			if (enddate == '') {
                RhUI.toast.show("请输入结束月份");
                return
            }
			var daterange = startdate + '~' + enddate;
            if (endqty == '') {
                RhUI.toast.show("请输入本期读数");
                return
            }
           
            modal.stop = true;
            var obj = {rid:rid,room:room,feeitem:feeitem,endqty:endqty,daterange:daterange};
            core.json('manage/readmeter', obj, function (json) {
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
	modal.initList = function(params) {
		modal.rid = params.rid;
		modal.room = params.room;		
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
		core.json('manage/myreadmeter', {
			page: modal.page,
			rid: modal.rid,
			room: modal.room
		}, function(ret) {			
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
			core.tpl('.container', 'tpl_readmeter_list', result, modal.page > 1);			
		})
	};
	return modal
});