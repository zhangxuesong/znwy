define(['core','tpl'], function(core, tpl) {
	var modal = {
		bid:0,
		page: 1,
		consume:0
	};
	modal.init = function(params) {
		modal.bid = params.bid;
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
		core.json('business/credit', {
			id:modal.bid,
			page: modal.page
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
			core.tpl('.container', 'tpl_creditlog_list', result, modal.page > 1);			
		})
	};
	modal.initGive = function(params) {	
		modal.minimumcharge = params.minimumcredit;		
		modal.creditrule = params.creditrule;
		$('#credit').bind('input propertychange', function() {			
			$('#btn-next').addClass('disabled');
			if ($(this).isNumber() && !$(this).isEmpty() && parseFloat($(this).val()) > 0) {
				$('#btn-next').removeClass('disabled');
			}
		});
		$('#btn-next').click(function() {
			var credit= $.trim($('#credit').val());				
			var mycredit= $.trim($('#mycredit').val());				
			if (!$.isEmpty(credit)) {
				credit = parseFloat(credit);
				mycredit = parseFloat(mycredit);
				if(mycredit >= credit){
					if ($.isNumber(credit) && credit > 0) {
						if (modal.minimumcharge > 0) {
							if (credit < modal.minimumcharge && modal.creditrule==0) {
								RhUI.toast.show('最低赠送' + modal.minimumcharge);
								return
							} 
						} 
					}
					else{
						RhUI.toast.show('赠送数量不能为空');
						return
					}
				}
				else{
					RhUI.toast.show('赠送数量不能大于当前数量');
					return
				}
			}
			else{
				RhUI.toast.show('赠送数量不能为空');
				return
			}
			var mobile= $.trim($('#mobile').val());	
			if ($.isEmpty(mobile)) {
				RhUI.toast.show('手机号不能为空');
				return
			}
			else{
				$('#btn-next').html('正在提交...').attr('stop', 1);
				core.json('business/checkmobile',{
				mobile: mobile
				}, function(ret) {
					if (ret.status != 1) {						
						RhUI.toast.show(ret.result.message);
						$('#btn-next').html('提交').removeAttr('stop');
						return
					}
					$("#myform").submit();						
				}, false, true);
			}			
		});		
	};
	modal.initSubmit = function(params) {	
		modal.minimumcharge = params.minimumcredit;	
		modal.creditrule = params.creditrule;
		$('#btn-next').click(function() {
			var credit= $.trim($('#credit').val());				
			var mycredit= $.trim($('#mycredit').val());				
			if (!$.isEmpty(credit)) {
				credit = parseFloat(credit);
				mycredit = parseFloat(mycredit);
				if(mycredit >= credit){
					if ($.isNumber(credit) && credit > 0) {
						if (modal.minimumcharge > 0) {
							if (credit < modal.minimumcharge && modal.creditrule==0) {
								RhUI.toast.show('最低赠送' + modal.minimumcharge);
								return
							} 
						} 
					}
					else{
						RhUI.toast.show('赠送数量不能为空');
						return
					}
				}
				else{
					RhUI.toast.show('赠送数量不能大于当前数量');
					return
				}
			}
			else{
				RhUI.toast.show('赠送数量不能为空');
				return
			}
			var uid = $.trim($('#uid').val());			
			var bid = $.trim($('#bid').val());	
			$('#btn-next').html('正在提交...').attr('stop', 1);
			core.json('business/givesubmit',{
				credit:credit,
				uid:uid,
				id:bid,
				consume:modal.consume 
				}, function(ret) {
					if (ret.status != 1) {						
						RhUI.toast.show(ret.result.message);
						$('#btn-next').html('提交').removeAttr('stop');
						return
					}
					RhUI.alert(ret.result.message, '', function() {					
						location.href = core.getUrl('business/mindex');					
					})
				}, false, true);
		});		
	};
	modal.initGive1 = function(params) {	
		modal.minimumcharge = params.minimumcost;		
		modal.minimumcredit = params.minimumcredit;	
		modal.creditrule = params.creditrule;	
		$('#credit').bind('input propertychange', function() {			
			$('#btn-next').addClass('disabled');
			if ($(this).isNumber() && !$(this).isEmpty() && parseFloat($(this).val()) > 0) {
				$('#btn-next').removeClass('disabled');
			}
		});
		$('#btn-next').click(function() {
			var credit = 0;
			var cost = $.trim($('#credit').val());				
			var mycredit= $.trim($('#mycredit').val());				
			if (!$.isEmpty(cost)) {
				if(modal.minimumcharge>0){
					credit = parseFloat(cost*modal.minimumcredit/modal.minimumcharge);
					mycredit = parseFloat(mycredit);
					if(mycredit >= credit){
						if ($.isNumber(credit) && credit > 0) {
							if (modal.minimumcharge > 0) {
								if (cost < modal.minimumcharge && modal.creditrule==0) {
									RhUI.toast.show('最低消费金额' + modal.minimumcharge);
									return
								} 
							} 
						}
						else{
							RhUI.toast.show('消费金额不能为空');
							return
						}
					}
					else{
						RhUI.toast.show('积分余额不足');
						return
					}
				}
				else{
					RhUI.toast.show('参数不正确');
					return
				}
			}
			else{
				RhUI.toast.show('消费金额不能为空');
				return
			}
			var mobile= $.trim($('#mobile').val());	
			if ($.isEmpty(mobile)) {
				RhUI.toast.show('手机号不能为空');
				return
			}
			else{	
				$('#btn-next').html('正在提交...').attr('stop', 1);
				core.json('business/checkmobile',{
				mobile: mobile
				}, function(ret) {
					if (ret.status != 1) {						
						RhUI.toast.show(ret.result.message);
						$('#btn-next').html('提交').removeAttr('stop');
						return
					}
					$("#myform").submit();						
				}, false, true);
			}			
		});		
	};
	modal.initSubmit1 = function(params) {	
		modal.minimumcharge = params.minimumcredit;
		modal.consume =  params.consume;
		$('#btn-next').click(function() {
			var credit= $.trim($('#credit').val());				
			var mycredit= $.trim($('#mycredit').val());				
			if (!$.isEmpty(credit)) {
				credit = parseFloat(credit);
				mycredit = parseFloat(mycredit);
				if(mycredit >= credit){
					if ($.isNumber(credit) && credit > 0) {
						if (modal.minimumcharge > 0) {
							if (credit < modal.minimumcharge && modal.creditrule==0) {
								RhUI.toast.show('最低消费' + modal.minimumcharge);
								return
							} 
						} 
					}
					else{
						RhUI.toast.show('消费金额不能为空');
						return
					}
				}
				else{
					RhUI.toast.show('账户余额不足');
					return
				}
			}
			else{
				RhUI.toast.show('消费金额不能为空');
				return
			}
			var uid = $.trim($('#uid').val());			
			var bid = $.trim($('#bid').val());	
			$('#btn-next').html('正在提交...').attr('stop', 1);
			core.json('business/givesubmit',{
				credit:credit,
				uid:uid,
				id:bid,
				consume:modal.consume 
				}, function(ret) {
					if (ret.status != 1) {						
						RhUI.toast.show(ret.result.message);
						$('#btn-next').html('提交').removeAttr('stop');
						return
					}
					RhUI.alert(ret.result.message, '', function() {					
						location.href = core.getUrl('business/mindex');					
					})
				}, false, true);
		});		
	};
	return modal
});