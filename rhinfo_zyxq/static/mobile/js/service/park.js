define(['core', 'tpl'], function (core, tpl) {
	var modal = {};
	modal.init = function(params) {	
		$('.parkopen1').unbind('click').click(function() {
			var self = $(this);
			var url = self.data("url");
			self.find('.fui-cell-icon').html('<i class="icon icon-yaochi"></i>'); 
			location.href = url;
			return
        });
		$('.parkopen2').unbind('click').click(function() {
			var self = $(this);
			var id = self.data("id"); 
			var lotid = self.data("lotid");
			var online = self.data("online");
			
			if(online==2){
			   RhUI.toast.show('设备不在线');
			   return
			}
		
			self.find('.fui-cell-icon').html('<i class="icon icon-yaochi"></i>'); 
			core.json('service/parkopen', {
				id: id,
				lotid:lotid
			}, function(ret) {			
				if (ret.status == 0) {
					self.find('.fui-cell-icon').html('<i class="icon icon-warn"></i>');
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.find('.fui-cell-icon').html('<i class="icon icon-lock"></i>'); },2000);
					return
				}
				if (ret.status == 2) {
					RhUI.toast.show(ret.result.msg);
					setTimeout(function(){location.href = ret.result.url;},2000);					
					return
				}
				if (ret.status == 11) {
					location.href = ret.result.url;
					return
				}
				self.find('.fui-cell-icon').html('<i class="icon icon-unlock"></i>'); 
				RhUI.toast.show('开闸成功');
				setTimeout(function(){location.href = core.getUrl('home/index');},2000);
			}, false, true)
        });
	};	
	return modal
});


