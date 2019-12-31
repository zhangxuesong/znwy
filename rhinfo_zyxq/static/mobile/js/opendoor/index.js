define(['core', 'tpl'], function (core, tpl) {
	var modal = {};
	modal.init = function(params) {		
		$('.fui-block-group-door .fui-block-child').unbind('click').click(function() {
			var self = $(this);
			var lockid = self.attr("id"); 
			var online = self.data("online");
			var qrcode = self.data("qrcode");
			
			var mylat = $("#mylat").val();
			var mylng = $("#mylng").val();	
			
			//二维码读头
			if(qrcode==1){
				location.href = core.getUrl('opendoor/qrcode') + "&id=" + lockid;
				return;
			}
			
			if(online==2){
			   RhUI.toast.show('设备不在线');
			   return
			}
			self.find('.icon').html('<i class="icon icon-yaochi"></i>'); 
			core.json('steward/opendoor', {
				id: lockid,
				mylat:	mylat,
				mylng:	mylng
			}, function(ret) {
				if (ret.status != 1) {
					self.find('.icon').html('<i class="icon icon-unlock"></i>'); 
					RhUI.toast.show('开门成功');
					setTimeout(function(){self.find('.icon').html('<i class="icon icon-lock"></i>'); },2000);
				}
				if (ret.status == 1) {
					self.find('.icon').html('<i class="icon icon-warn"></i>');
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.find('.icon').html('<i class="icon icon-lock"></i>'); },2000);
				}
				setTimeout(function(){location.reload();},6000);
			}, false, true)
        });
		$('.group-door .fui-block-child').unbind('click').click(function() {
			var self = $(this);
			var lockid = self.attr("id"); 
			var online = self.data("online");
			var qrcode = self.data("qrcode");
			var mylat = $("#mylat").val();
			var mylng = $("#mylng").val();
			
			//二维码读头
			if(qrcode==1){
				location.href = core.getUrl('opendoor/qrcode') + "&id=" + lockid;
				return;
			}
			
			if(online==2){
			   RhUI.toast.show('设备不在线');
			   return
			}
			self.find('.icon').html('<i class="icon icon-yaochi"></i>'); 
			core.json('steward/opendoor', {
				id: lockid,
				mylat:	mylat,
				mylng:	mylng
			}, function(ret) {
				if (ret.status != 1) {
					self.find('.icon').html('<i class="icon icon-unlock"></i>'); 
					RhUI.toast.show('开门成功');
					setTimeout(function(){self.find('.icon').html('<i class="icon icon-lock"></i>'); },2000);
				}
				if (ret.status == 1) {
					self.find('.icon').html('<i class="icon icon-warn"></i>');
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.find('.icon').html('<i class="icon icon-lock"></i>'); },2000);
				}
				setTimeout(function(){location.reload();},6000);				
			}, false, true)
        });
		wx.miniProgram.getEnv(function(res){
			if(res.miniprogram){
				$(".fui-header-right").find("#myblue").show();
			}
		});
	};
	modal.initShare = function() {		
		$('#btn-share').click(function() {
				$('#cover').fadeIn(200)
			});
		$('#cover').click(function() {
			$('#cover').hide()
		})
	};
	modal.initVisit = function() {
	//	var reqParams = ['rhui.picker'];
	//	require(reqParams, function() {			
	//		$('#effedate').datePicker()
	//	});	
		$('#btnSubmit').click(function(){			
			var params = [];
			params['id'] = $("#doorid").val();
			params['effedate'] = $("#effedate").val();
			params['opentimes'] = $("#opentimes").val();
			if(params['effedate']==""){
			   RhUI.toast.show('有效时间不能为空');
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
	modal.initAsk = function() {		
		$('#btnSubmit').click(function(){	
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			var params = [];
			params['id'] = $("#doorid").val();
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
			//   RhUI.toast.show('来访事由不能为空');
			//   return
			}
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			$("#myform").submit();
		});
	};
	modal.initAskmobile = function() {
		$('#btnSubmit').click(function(){			
			var params = [];
			params['id'] = $("#doorid").val();
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
			//   RhUI.toast.show('来访事由不能为空');
			//   return
			}
			$("#myform").submit();
		});
	};
	modal.initAudit = function() {		
		$('#btn-audit').unbind('click').click(function(){
			var visitid = $("#visitid").val(); 
			core.json('opendoor/auditvisit', {
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
			core.json('opendoor/auditvisit', {
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
		$('#btn-opendoor').unbind('click').click(function() {
			var self = $(this);
			var lockid = $("#doorid").val(); 
			self.html('<i class="icon icon-yaochi"></i> 开门'); 
			core.json('opendoor/opendoor_visit', {
				id: lockid				
			}, function(ret) {
				if (ret.status != 1) {
					self.html('<i class="icon icon-unlock"></i> 开门'); 
					RhUI.toast.show('开门成功');
					setTimeout(function(){self.html('<i class="icon icon-lock"></i> 开门'); },2000);
				}
				if (ret.status == 1) {
					self.html('<i class="icon icon-warn"></i> 开门');
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.html('<i class="icon icon-lock"></i> 开门'); },2000);
				}
			}, false, true)
		})
	};
	modal.initFace = function(params) {
		modal.params = params;		
		$('.fui-uploader').click(function(){				
			core.wxUploadImages($(this),"images",core.getUrl('util/wxuploader')+'&nodisplay=1',core.getUrl('util/remove'));	
		});
		
		$("#other").on('click',function () {
			var ischecked = $('#other').is(':checked');
			if(ischecked){
				$('#otherdisplay').show();		
			}
			else{
				$('#otherdisplay').hide();
			}
		});
		$('#btnSubmit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			var ischecked=$('#other').is(':checked');
			var mobile = '';
			var nickname = '';
			var isother = 0;
			if(ischecked){
				if (!$('#mobile').isMobile()) {
					RhUI.toast.show('请输入11位手机号码');
					return
				}
				mobile = $("#mobile").val();
				nickname = $("#nickname").val();				
				if(nickname==""){
				   RhUI.toast.show('姓名不能为空');
				   return
				}
				isother = 1;
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'));
			});
			var url = 'opendoor/facedoor';
			if(modal.params.doors==1){
				url = 'opendoor/regfacedoor';
			}
			core.json(url, {
				'id':modal.params.id,
				'images': images,
				'mobile':mobile,
				'nickname':nickname,
				'isother':isother
			}, function(ret) {
				if (ret.status == 1) {
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){
						location.href = core.getUrl('home/index');
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('立即认证');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		});
	};
	return modal
});


