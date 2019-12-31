define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		modal.params = params;		
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('util/uploader')+'&nodisplay=1',
			removeUrl: core.getUrl('util/remove')
		});
		$('#btnSubmit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			var that = $(this);
			
			$('.fui-images').each(function() {
				var image = $(this).data('image');
				$("input[name="+image+"]").val($(this).find('li').data('filename'));			
			});
			
			RhUI.confirm('确认提交吗？','报名确认',function(){					
				that.html('正在处理...').attr('stop', 1);						
				core.json('activity/activity',
				$("#myform").serializeObject(),
				function(rjson) {
					if (rjson.status != 1) {
						that.html('提交').removeAttr('stop');					
						RhUI.toast.show(rjson.result.message);
						return
					}
					RhUI.toast.show(rjson.result.message);	
					location.href = core.getUrl('home/index');
				})
			})
		})
	};	
	return modal
});