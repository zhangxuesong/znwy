define(['core', 'tpl'], function(core, tpl) {
	var modal = {totalfee:0,billids:''};
	modal.init = function(params) {
		modal.totalfee = params.totalfee;
		modal.billids = params.billids;
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
			totalfee = totalfee.toFixed(1);
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
				location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&billid='+ modal.billids;			
			}
		});
		
	};		
	modal.initYear = function(params) {
		modal.totalfee = params.totalfee;		
		modal.creditfee = params.creditfee;
		modal.iswap = params.iswap;
		
		$(".allCheck").on('click', function (e) {
			e.stopPropagation();
		})
		$(".mui-navigate").on('click', function (e) {
			$(this).parent().toggleClass('mui-active');
		})
		
		modal.isAllCheck(); 
		modal.count();	
		modal.isDisabled();
		
		$(".allCheck").each(function(){
			$(this).click(function () {
				var $that=$(this);
				if ($(this).prop('checked')) {
					$(this).closest('ul').find('.fees-info :checkbox').prop('checked', true);
					modal.isDisabled();
				} else {
					var myDate = new Date();
					var month=myDate.getMonth()+1;
					var year=myDate.getFullYear();
					var $collapse=$(this).closest('ul').find('.mui-collapse-content');
					$collapse.each(function (idx) {
						var $this = $(this);
						var singleCheck = month;
						$this.find(':checkbox').each(function (idx) {
							var cycleVal=$(this).parent().find('.cycleVal').val().split('-');
							var cycleYear=parseInt(cycleVal[0]);
							var cycleMonth=parseInt(cycleVal[1]);
							var index = idx + 1;
							if(year==cycleYear){         //当前年
								if (index < singleCheck) {
									$(this).prop('disabled', true);
								} else {
									if(cycleMonth>=month){
										$(this).prop('disabled', false).prop('checked',false);
									}
								}
							}
							if(year<cycleYear){
								$that.closest('ul').find('.fees-info :checkbox').prop('disabled',false).prop('checked', false);
							}							
						})
					})
					
					
				}
				modal.count();
			})
		})

		// 单机每个复选框(非全选的)			
		$(".fees-info :checkbox").click(function () {
			modal.isAllCheck();
			modal.isDisabled();
			var i = $(this).parent().index();
			var $collapse = $(this).closest('ul').find('.mui-collapse-content');
			
			$collapse.each(function (idx) { 
				$(this).find(':checkbox').each(function (idx) {					
					if (idx < i ) {
						$(this).prop('disabled', true).prop('checked', true);
						modal.isDisabled()
					}
					modal.isAllCheck();
				})
			})
			modal.count();
		})
		
		$('#billpay').click(function() {
			if(modal.totalfee==0 || modal.billids==''){
				RhUI.toast.show('请选择要结算账单');
			}
			else {
				var ischecked = $('#mycredit').is(':checked');				
				if(ischecked){
					modal.totalfee = modal.totalfee - modal.creditfee;
				}
				else{
					modal.creditfee = 0;
				}
				if(modal.iswap==1){
					location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&creditfee='+ modal.creditfee + '&billid='+ modal.billids + '&feetype=1&iswxapp=0';	
				}
				else{
					wx.miniProgram.getEnv(function(res) {
						if(res.miniprogram){
							location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&creditfee='+ modal.creditfee + '&billid='+ modal.billids + '&feetype=1&iswxapp=1';							
						}
						else{
							location.href = core.getUrl('mypay') + '&fee='+ modal.totalfee + '&creditfee='+ modal.creditfee + '&billid='+ modal.billids + '&feetype=1&iswxapp=0';	
						}
					})						
				}				
			}
		});
		
	};		
	modal.count = function() { //计数 先计算各自的再合计
		var allCount=0;
		var billids = '';
		$(".mui-collapse-content").each(function () {
			var count = 0;			
			var $this = $(this);
			$(this).find(".singleCost").each(function (idx) {
				if (idx < $this.find(':checked').length) {
					count += parseFloat($(this).val());
					billids += $this.find(':checked').val() + ',';
				}
				$this.parent().find('.count font').text(count + '元');
			})
			allCount+=count
		})
		$("#totalfee").text('￥'+allCount);
		modal.totalfee = allCount.toFixed(2);
		modal.billids = billids.substring(0,billids.length - 1);
		if(modal.totalfee>0){
			$("#billpay").attr("disabled",false);
		}
		else{
			$("#billpay").attr("disabled",true);
		}
		var iscreditpay = parseInt($("#iscreditpay").val());
		if(iscreditpay=='1'){
			var paycredit = parseInt($("#paycredit").val());
			var paycost = parseInt($("#paycost").val());
			var payrate = parseInt($("#payrate").val());
			var mycredit = parseInt($("#mycredit").val());
			var creditcost = parseInt(mycredit * paycost / paycredit) ;
			var creditcost1 = parseInt(totalfee * payrate / 100);
			if(creditcost > creditcost1){
				creditcost = creditcost1;
			}
			$("#mylabel").html('使用积分可抵扣<font style="color:red;vertical-align:top;">' + creditcost + '</font>元');					
			modal.creditfee = creditcost;
		}
	};
	modal.isAllCheck = function() {
		$(".mui-collapse-content").each(function () {
			if ($(this).find(':checked').length == $(this).find(':checkbox').length) {
				$(this).closest('ul').find('.allCheck').prop('checked', true);
			} else {
				$(this).closest('ul').find('.allCheck').prop('checked', false);					
			}
		})
	};
	modal.isDisabled = function() { //之前的复选框是否被禁用,默认要求所有的复选框全选
		var myDate = new Date();
		var month=myDate.getMonth()+1;
		var year=myDate.getFullYear();
		$(".mui-collapse-content").each(function (idx) {
			var $this = $(this);
			var singleCheck = $this.find(':checked').length;
			$this.find(':checkbox').each(function (idx) {
				var cycleVal=$(this).parent().find('.cycleVal').val().split('-');
				var cycleYear=parseInt(cycleVal[0]);
				var cycleMonth=parseInt(cycleVal[1]);
				var index = idx + 1;
				if(year==cycleYear){         //当前年
					if (index < singleCheck) {
						$(this).prop('disabled', true);
					} else {
						if(cycleMonth>=month){
							$(this).prop('disabled', false);
						}
					}
				}
				if(cycleYear>year){
					if (index < singleCheck) {      //明年,之后年
						$(this).prop('disabled', true);
					} else {
						$(this).prop('disabled', false);
					}
				}
				if(cycleYear<year){
					$(this).prop('disabled', true);  //之前的欠款全部全选、禁用
				}
			})
		})
	};
	return modal
});