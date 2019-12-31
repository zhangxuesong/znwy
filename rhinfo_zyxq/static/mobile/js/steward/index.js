define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function() {
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
			var mylat = $("#mylat").val();
			var mylng = $("#mylng").val();
			if(online==2){
			   RhUI.toast.show('设备不在线');
			   return
			}
			self.find('.icon').html('<i class="icon icon-yaochi"></i>'); 
			core.json('steward/opendoor', {
				id:	lockid,
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
	};	
	return modal
});


