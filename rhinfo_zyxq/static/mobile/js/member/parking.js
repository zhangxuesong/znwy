define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.initList = function() {		
		$('*[data-toggle=delete]').unbind('click').click(function() {
			var item = $(this).closest('.address-item');
			var id = item.data('addressid');
			RhUI.confirm('删除后无法恢复, 确认要删除吗 ?', function() {
				core.json('member/delete', {
					id: id
				}, function(ret) {
					if (ret.status == 1) {						
						item.remove();
						setTimeout(function() {
							if ($(".address-item").length <= 0) {
								$('.content-empty').show()
							}
						}, 100);
						return
					}
					RhUI.toast.show(ret.result.message)
				}, true, true)
			})
		});	
		$('*[data-toggle=control]').unbind('click').click(function() {
			var self = $(this);
			var id = self.data('parkid');
			var online =  self.data('online');
			if(online==0){
			   RhUI.toast.show('不在线');
			   return
			}
			var updown =  self.data('updown');
			var updowntxt1 = updown==1?'上升':'下降';
			var updowntxt2 = updown==1?'下降':'上升';
			if(updown==1){
				updown = 0;
			}
			else{
				updown = 1;
			}
			RhUI.confirm('确认要' + updowntxt1 + '操作吗 ?', function() {
				self.html('<i class="icon icon-yaochi text-warning"></i>' + updowntxt1); 
				core.json('car/parklock', {
					id: id,
					updown:updown
				}, function(ret) {
					if (ret.status == 0) {
						self.html('<i class="icon icon-warn text-warning"></i>' + updowntxt1);
						RhUI.toast.show(ret.result.message);
						setTimeout(function(){self.html('<i class="icon icon-lock text-warning"></i>' + updowntxt1); },2000);
						return
					}
					self.html('<i class="icon icon-unlock text-warning"></i>' + updowntxt2); 
					self.data('updown',updown);
					RhUI.toast.show(ret.result.message);
					setTimeout(function(){self.html('<i class="icon icon-lock text-warning"></i> '+ updowntxt2); },2000);
				}, false, true);
			});
		});		
	};
	return modal
});