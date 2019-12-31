define(['core', 'tpl'], function(core, tpl) {
	var modal = {totalfee:0,rid:0,billids:'',count:0,room:'',myroom:'',myurl:''};
	modal.init = function(params) {
		modal.totalfee = params.totalfee;
		modal.billids = params.billids;
		modal.count = params.count;
		modal.room = params.room;
		modal.myroom = params.myroom;
		modal.myurl = params.myurl;
		modal.rid = params.rid;
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
					billids += billid + '-';					
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
				location.href = modal.myurl + '&fee='+ modal.totalfee + '&billids='+ modal.billids + '&room=' + modal.room + '&myroom=' + modal.myroom;			
			}
		});
		
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
	return modal
});