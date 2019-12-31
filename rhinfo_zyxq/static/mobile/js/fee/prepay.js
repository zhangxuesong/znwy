define(['core','tpl'], function(core, tpl) {
	var modal = {page:1,totalfee:0,billids:'',count:0,iswap:0};
	modal.init = function() {		
		$('#btnSubmit').click(function(){			
			var selecthid = $("#hid").val();
			var selectfeeitemid = $("#feeitemid").val();
			if(selecthid==''){
			   RhUI.toast.show('房产不能为空');
			   return
			}
			if(selectfeeitemid==''){
			   RhUI.toast.show('收费项目不能为空');
			   return
			}
			$("#myform").submit();			
		});
	};
	modal.initBill = function(params) {
		$(".minus").unbind('click').click(function () {
			var qty = $("#qty").val();	
			if(qty>1){
				qty = qty - 1;
				$("#qty").val(qty);
			}
        })
		$(".plus").unbind('click').click(function () {
			var qty = $("#qty").val();
			if(!$.isNumber(qty)){
				qty = 0;
			}
			qty =  qty*1 + 1;
			$("#qty").val(qty);	
        })
		$('#btnSubmit').click(function(){
			if ($('#btnSubmit').attr('stop')) {
				return
			}			
			$('#btnSubmit').html('<i class="icon icon-money"></i> 正在创建...').attr('stop', 1);			
			core.json('fee/createbill', {
				mid: $("#hid").val(),
				feeitemid: $("#feeitemid").val(),
				qty: $("#qty").val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('<i class="icon icon-money"></i> 生成账单').removeAttr('stop');
					return
				}
				RhUI.toast.show(ret.result.message);
				$("#myform").submit();
			},true, true)			
		});
	};		
	return modal
});