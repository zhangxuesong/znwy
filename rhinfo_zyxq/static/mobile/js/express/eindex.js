define(['core', 'tpl'], function (core, tpl) {
    var modal = {
		sid:0,
		page:1,
		compid:0
	};
    modal.init = function (params) {
		modal.sid = params.sid;
		modal.compid = params.compid;
        core.json('express/get_uid_today', {sid:modal.sid,compid:modal.compid}, function (json) {
            if (json.status == 1) {
                $("#today_repair").text(json.result.today_repair);
                $("#today_suggest").text(json.result.today_suggest);
                $("#today_member").text(json.result.today_member)
            }
        });
		core.json('express/get_compid_express', {sid:modal.sid,compid:modal.compid}, function (json) {
            if (json.status == 1) {
                $("#express_take").text(json.result.express_take);
                $("#express_send").text(json.result.express_send);
                $("#express_notice").text(json.result.express_notice)
            }
        });
		core.json('express/get_comp_fee', {sid:modal.sid,compid:modal.compid}, function (json) {
            if (json.status == 1) {
                $("#no_balance").html('取件<font class="text-danger" style="font-size:0.7rem;">' + json.result.payfee_no2 + '</font> 寄件<font class="text-blue" style="font-size:0.7rem;">' + json.result.payfee_no1 + '</font>');
                $("#month_balance").html('取件<font class="text-danger" style="font-size:0.7rem;">' + json.result.payfee_month2 + '</font> 寄件<font class="text-blue" style="font-size:0.7rem;">' + json.result.payfee_month1+ '</font>');
                $("#today_balance").html('取件<font class="text-danger" style="font-size:0.7rem;">' + json.result.payfee_today2 + '</font> 寄件<font class="text-blue" style="font-size:0.7rem;">' + json.result.payfee_today1+ '</font>');
            }
        });
		$("#mystore").unbind('click').click(function () {
            var html = $(".pop-store-hidden").html();
            var container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });  
			$('.verify-pop').find('.fui-icon-col').unbind('click').click(function () {
                container.close()
            }); 			
        });
		$("#mycomp").unbind('click').click(function () {
            var html = $(".pop-comp-hidden").html();
            var container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });  
			$('.verify-pop').find('.fui-icon-col').unbind('click').click(function () {
                container.close()
            }); 			
        })
    };
	 modal.initNext = function (params) {
		modal.sid = params.sid;
		$('#btn-next').click(function() {			
			var mobile= $.trim($('#mobile').val());	
			if ($.isEmpty(mobile)) {
				RhUI.toast.show('手机号不能为空');
				return
			}
			else{
				core.json('express/checkmobile',{
				mobile: mobile
				}, function(ret) {
					if (ret.status != 1) {						
						RhUI.toast.show(ret.result.message);
						return
					}
					$("#myform").submit();						
				}, false, true);
			}			
		});		
    };
	 modal.initEmp = function (params) {
		modal.sid = params.sid;
		 $('*[data-toggle=delete]').unbind('click').click(function() {
            var obj = $(this);					
			var id = obj.data('id');
			RhUI.confirm('确认删除吗?', function() {
				core.json('express/delemp', {
					id: id,
					sid: modal.sid
					}, function(ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);					
							return
						}
						obj.parents(".fui-list").remove();	
						RhUI.toast.show(ret.result.message);
					},false, true)
			})		
        })
    };
	 modal.initSubmit = function (params) {
		modal.sid = params.sid;
		$('#btn-next').click(function() {
			var mobile= $.trim($('#mobile').val());	
			var remark= $.trim($('#remark').val());			
			var uid = $.trim($('#uid').val());	
			var title= $.trim($('#title').val());	
			if(title==""){
				RhUI.toast.show('姓名不能为空');
				return
			}
			core.json('express/addemp',{
				uid:uid,
				mobile:mobile,
				title:title,
				remark:remark,
				sid:modal.sid
				}, function(ret) {
					if (ret.status != 1) {						
						RhUI.toast.show(ret.result.message);
						return
					}
					RhUI.alert(ret.result.message, '', function() {					
						location.href = core.getUrl('express/employee') + "&sid=" + params.sid;					
					})
				}, false, true);
		});		
    };
	return modal
});