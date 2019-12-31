define(['core', 'tpl'], function(core, tpl) {
	var modal = {totalfee:0,billids:'',count:0,creditfee:0,iswap:0};
	modal.init = function(params) {
		modal.totalfee = params.totalfee;
		modal.billids = params.billids;
		modal.count = params.count;
		modal.creditfee = params.creditfee;
		modal.iswap = params.iswap;
		$('.check-item').unbind('click').click(function() {			
			var totalfee = 0;
			var billids = '';
			var currid = $(this).attr('id');
			var i = currid.substr(3);
			$('.feebill-item').each(function(index,obj) {
				var fee = parseFloat($(this).data('fee'));
				var billid = $(this).data('billid');
				var j = index -1;
				if ($(this).find('.check-item').prop('checked')) {
					totalfee += fee;	
					billids += billid + ',';					
					if(i==index){												
						$("#fee"+j).attr("disabled",true);
						$("#fee"+index).attr("disabled",false);						
					}
					else if(i==j){
						$("#fee"+index).attr("disabled",false);
					}
					else{
						$("#fee"+index).attr("disabled",true);
					}
				}
				else{
					if(i==index){
						$("#fee"+j).attr("disabled",false);
						$("#fee"+index).attr("disabled",false);					
					}
					else if(i==j){
						$("#fee"+index).attr("disabled",true);
					}
					else{
						$("#fee"+index).attr("disabled",true);
					}
				}
				
			});
			totalfee = totalfee.toFixed(2);
			$("#totalfee").html('￥'+ totalfee);
			modal.totalfee = totalfee;
			modal.billids = billids.substring(0,billids.length - 1);

			if(modal.totalfee>0){
				$("#billpay").attr("disabled",false);
			}
			else{
				$("#billpay").attr("disabled",true);
			}
		});
		
		$('#billpay').click(function() {			
			if(modal.totalfee==0 || modal.billids==''){
				RhUI.toast.show('请选择要结算账单');
			}
			else {						
				if(modal.iswap==1){
					location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&creditfee='+ modal.creditfee + '&billid='+ modal.billids + '&feetype=2&iswxapp=0';	
				}
				else{
					wx.miniProgram.getEnv(function(res) {
						if(res.miniprogram){
							location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&creditfee='+ modal.creditfee + '&billid='+ modal.billids + '&feetype=2&iswxapp=1';							
						}
						else{
							location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&creditfee='+ modal.creditfee + '&billid='+ modal.billids + '&feetype=2&iswxapp=0';	
						}
					})						
				}				
			}
		});
		
	};		
	return modal
});