define(['core','tpl'], function(core, tpl) {
	var modal = {page:1,totalfee:0,billids:'',count:0,iswap:0};
	modal.init = function() {		
		$('#btnSubmit').click(function(){			
			var selectrid = $("#rid").val();
			var selectlesseeid = $("#lesseeid").val();
			if(selectrid==''){
			   RhUI.toast.show('收费单位不能为空');
			   return
			}
			if(selectlesseeid==''){
			   RhUI.toast.show('承租人不能为空');
			   return
			}
			$("#myform").submit();			
		});
	};
	modal.initPay = function(params) {
		modal.totalfee = params.totalfee;
		modal.billids = params.billids;
		modal.count = params.count;
		modal.iswap = params.iswap;
		$('.check-item').unbind('click').click(function() {			
			var totalfee = 0;
			var billids = '';
			var currid = $(this).attr('id');
			var i = currid.substr(3);
			var j=0;  //前
			var k=0;  //后
			if(modal.count>1){
				if(i==0){
					k =  parseInt(i) + 1;
				}
				else if(modal.count> parseInt(i) + 1){
					j = parseInt(i) - 1;
					k = parseInt(i) + 1;
				}
				else{
					j = parseInt(i) - 1;
				}				
			}	
			
			$('.feebill-item').each(function(index,obj) {
				var fee = parseFloat($(this).data('fee'));
				var billid = $(this).data('billid');				
				if ($(this).find('.check-item').prop('checked')) {
					totalfee += fee;	
					billids += billid + ',';					
					if(i==index){
						$("#fee"+j).attr("disabled",true);								
						$("#fee"+k).attr("disabled",false);
						$("#fee"+index).attr("disabled",false);
					}
					else if(k==index){
						$("#fee"+index).attr("disabled",false);
					}
					else{
						$("#fee"+index).attr("disabled",true);
					}
				}
				else{					
					if(i==index){
						$("#fee"+j).attr("disabled",false);
						$("#fee"+k).attr("disabled",true);
						$("#fee"+index).attr("disabled",false);						
					}
					else if(k==index){
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
					location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&billid='+ modal.billids + '&feetype=11&iswxapp=0';	
				}
				else{
					wx.miniProgram.getEnv(function(res) {
						if(res.miniprogram){
							location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&billid='+ modal.billids + '&feetype=11&iswxapp=1';							
						}
						else{
							location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&billid='+ modal.billids + '&feetype=11&iswxapp=0';	
						}
					})						
				}				
			}
		});
		
	};		
	return modal
});