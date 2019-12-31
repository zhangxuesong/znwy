define(['core', 'tpl'], function (core, tpl) {
	var modal = {};
	modal.init = function(params) {	
		$('.fui-block-group-door .fui-block-child').unbind('click').click(function() {
			var self = $(this);
			var lockid = self.attr("id"); 
			var online = self.data("online");
			var visitid = self.data("visitid");
			var qrcode = self.data("qrcode");
			
			var mylat = $("#mylat").val();
			var mylng = $("#mylng").val();
			var visitid = $("#visitid").val();
			
			//二维码读头
			if(qrcode==1){
				location.href = core.getUrl('opendoor/qrcode') + "&id=" + lockid + "&visitid=" + visitid;
				return;
			}
			
			if(online==2){
			   RhUI.toast.show('设备不在线');
			   return
			}
			self.find('.icon').html('<i class="icon icon-yaochi"></i>'); 
			core.json('opendoor/opendoor', {
				id: lockid,
				mylat:	mylat,
				mylng:	mylng,
				visitid:visitid
			}, function(ret) {
				if (ret.status != 1) {
					self.find('.icon').html('<i class="icon icon-unlock"></i>'); 
					var times = $("#opentimes").html();
					$("#opentimes").html(times - 1);
					RhUI.toast.show('开门成功');					
					setTimeout(function(){self.find('.icon').html('<i class="icon icon-lock"></i>'); },2000);
				}
				if (ret.status == 1) {
					self.find('.icon').html('<i class="icon icon-warn"></i>');
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.find('.icon').html('<i class="icon icon-lock"></i>'); },2000);
				}
			}, false, true)
        });
	};
	modal.initVisit = function(params) {	
		$('.fui-card-btns .opendoor').unbind('click').click(function() {
			var self = $(this);
			var lockid = self.data("doorid"); 
			var visitid = self.data("visitid");
			var qrcode = self.data("qrcode");
			
			var mylat = $("#mylat").val();
			var mylng = $("#mylng").val();
			
			//二维码读头
			if(qrcode==1){
				location.href = core.getUrl('opendoor/qrcode') + "&id=" + lockid + "&visitid=" + visitid;
				return;
			}
			
			self.find('span').html('<i class="icon icon-yaochi"></i>'); 
			core.json('opendoor/opendoor', {
				id: lockid,
				mylat:	mylat,
				mylng:	mylng,
				visitid:visitid
			}, function(ret) {
				if (ret.status != 1) {
					self.find('span').html('<i class="icon icon-unlock"></i>'); 
					var times = $("#opentimes" + visitid).html();
					$("#opentimes" + visitid).html(times - 1);
					RhUI.toast.show('开门成功');					
					setTimeout(function(){self.find('span').html('<i class="icon icon-lock"></i>'); },2000);
				}
				if (ret.status == 1) {
					self.find('span').html('<i class="icon icon-warn"></i>');
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.find('span').html('<i class="icon icon-lock"></i>'); },2000);
				}
			}, false, true)
        });
	};
	
	modal.initList = function(params) {	
		$('.fui-card-btns .opendoor').unbind('click').click(function() {
			var self = $(this);
			var lockid = self.data("doorid"); 
			var visitid = self.data("visitid");
			var qrcode = self.data("qrcode");
			
			var mylat = $("#mylat").val();
			var mylng = $("#mylng").val();
			
			//二维码读头
			if(qrcode==1){
				location.href = core.getUrl('opendoor/qrcode') + "&id=" + lockid + "&visitid=" + visitid;
				return;
			}
			
			self.find('.icon').html('<i class="icon icon-yaochi"></i>'); 
			core.json('opendoor/opendoor', {
				id: lockid,
				mylat:	mylat,
				mylng:	mylng,
				visitid:visitid
			}, function(ret) {
				if (ret.status != 1) {
					self.find('.icon').html('<i class="icon icon-unlock"></i>'); 
					var times = $("#opentimes" + visitid).html();
					$("#opentimes" + visitid).html(times - 1);
					RhUI.toast.show('开门成功');					
					setTimeout(function(){self.find('.icon').html('<i class="icon icon-lock"></i> 开门'); },2000);
				}
				if (ret.status == 1) {
					self.find('.icon').html('<i class="icon icon-warn"></i>');
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.find('.icon').html('<i class="icon icon-lock"></i> 开门'); },2000);
				}
			}, false, true)
        });
	};

	return modal
});


