define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		modal.params = params;
		$(".elevator-item").on('click',function(){
			if(!$(this).hasClass('active')){
				$(".elevator-item").removeClass('active');
				$(this).addClass('active');
				$("#floor").val($(this).attr("data-floor"))
			}
		});
		$('#btnSubmit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#floor').isEmpty()) {
				RhUI.toast.show('请选择楼层');
				return
			}			
			var that = $(this);			
			RhUI.confirm('确认楼层正确吗？','楼层确认',function(){					
				that.attr('stop', 1);
				core.json('elevator/index', {
					'devsn':modal.params.devsn,
					'floor':$("#floor").val()
				}, function(rjson) {
					if (rjson.status == 0) {
						that.html('提交').removeAttr('stop');					
						RhUI.toast.show(rjson.result.message);
						return
					}					
					RhUI.toast.show(rjson.result.message);
					setTimeout(function(){
						location.href = core.getUrl('home/index');
					},1000);	
				}, false, true)
			})
		});
		
	};	
	return modal
});