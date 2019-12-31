define(['core', 'tpl'], function (core, tpl) {
	var modal = {};
	modal.init = function(params) {		
		modal.params = params;
		$('#btnSubmit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			core.json('manager/visitscan',{visitid:modal.params.visitid},
			function(rjson){		
				if (rjson.status == 0) {								
					RhUI.toast.show(rjson.result.message);
					return
				}
				RhUI.toast.show(rjson.result.message);
				setTimeout(function(){
					WeixinJSBridge.call("closeWindow");
				},2000);	
			},false,true);	
		});
	};
	modal.initVisit = function() {
		$('#btnSubmit').click(function(){			
			var params = [];
			params['effedate'] = $("#effedate").val();
			params['opentimes'] = $("#opentimes").val();
			if(params['effedate']==""){
			   RhUI.toast.show('有效时间不能为空');
			   return
			}
			$("#myform").submit();			
		});
	};
	modal.initAsk = function() {		
		$(".category-type a").on("click", function() {
			$("#reason").val($(this).attr("data-reason"))
		});
		$('#btnSubmit').click(function(){	
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			var params = [];
			params['effedate'] = $("#effedate").val();
			params['opentimes'] = $("#opentimes").val();
			params['room'] = $('#room').val();
			params['reason'] = $('#reason').val();
			if(params['room']==''){
				RhUI.toast.show('请选择房产');
				return
			}
			if(params['effedate']==""){
			   RhUI.toast.show('有效时间不能为空');
			   return
			}
			if(params['reason']==""){
			   RhUI.toast.show('来访事由不能为空');
			   return
			}
			var ischecked=$('#iscar').is(':checked');
			var carno ='';
			if(ischecked){
				carno = $(".car_input").attr("data-pai");			
				if(carno !='' && typeof(carno) !='undefined'){
					var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
					if(carno.search(re) == -1) {
						RhUI.toast.show('车牌输入不正确');
						return 
					}				
				}
				else{
					RhUI.toast.show('车牌不能为空');
					return
				}	
			}
			$('#carno').val(carno);
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			$("#myform").submit();
		});
	};
	modal.initAskmobile = function() {
		$(".category-type a").on("click", function() {
			$("#reason").val($(this).attr("data-reason"))
		});
		$('#btnSubmit').click(function(){			
			var params = [];
			params['effedate'] = $("#effedate").val();
			params['opentimes'] = $("#opentimes").val();
			params['mobile'] = $('#mobile').val();
			params['reason'] = $('#reason').val();
			if(params['mobile']==''){
				RhUI.toast.show('请输入手机号码');
				return
			}
			if(params['effedate']==""){
			   RhUI.toast.show('有效时间不能为空');
			   return
			}
			if(params['reason']==""){
			   RhUI.toast.show('来访事由不能为空');
			   return
			}
			var ischecked=$('#iscar').is(':checked');
			var carno ='';
			if(ischecked){
				carno = $(".car_input").attr("data-pai");			
				if(carno !='' && typeof(carno) !='undefined'){
					var re = /^[\u4e00-\u9fa5]{1}[0-9a-zA-Z]{1}[0-9a-zA-Z挂学警军港澳]{5,6}$/;
					if(carno.search(re) == -1) {
						RhUI.toast.show('车牌输入不正确');
						return 
					}				
				}
				else{
					RhUI.toast.show('车牌不能为空');
					return
				}	
			}
			$('#carno').val(carno);
			$("#myform").submit();
		});
	};
	modal.initAudit = function() {		
		$('#btn-audit').unbind('click').click(function(){
			var visitid = $("#visitid").val(); 
			core.json('visit/auditvisit', {
				id: visitid,
				vstatus:1
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.alert(ret.result.message, '', function() {					
						 location.href = core.getUrl('home/index');					
					})
					return
				}
				RhUI.toast.show(ret.result.message);
			}, false, true)
		});
		$('#btnSubmitno').unbind('click').click(function(){
			var visitid = $("#visitid").val(); 
			core.json('visit/auditvisit', {
				id: visitid,
				reason:$("#reason").val(),
				vstatus:2
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.alert(ret.result.message, '', function() {					
						 location.href = core.getUrl('home/index');					
					})
					return
				}
				RhUI.toast.show(ret.result.message);
			}, false, true)
		});
	};
	return modal
});


