define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		modal.params = params;		
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('util/uploader')+'&nodisplay=1',
			removeUrl: core.getUrl('util/remove')
		});
		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-type"))
			}
		});
		$(".address-item").on('click',function(){
			var id = $(this).data('addressid');
			core.json('member/setdefault', {
				id: id
			}, function(ret) {
				if (ret.status == 1) {
					location.href = core.getUrl('steward/suggest');
				}
				return
			}, true, true)
		});
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#category_type').isEmpty()) {
				RhUI.toast.show('请选择建议类别');
				return
			}
			
			if ($('#content').isEmpty()) {
				RhUI.toast.show('投诉建议不能为空');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			var content = $('#content').val();
			var category = $('#category_type').val();
			RhUI.confirm('确认内容填写正确吗?',function(){		
				$(this).html('正在处理...').attr('stop', 1);
				core.json('steward/suggest', {
					'rid':modal.params.rid,
					'cid':category,
					'images': images,
					'content': content
				}, function(ret) {
					if (ret.status == 0) {
						$('.btn-submit').removeAttr('stop').html('提交成功')
						setTimeout(function(){
							location.href = core.getUrl('service/suggest');
						},1000);		
						return
					}
					$('.btn-submit').removeAttr('stop').html('提交');
					RhUI.toast.show(ret.result.message)
				}, true, true)
			});
		})
	};
	modal.initReply = function(params) {
		modal.params = params;		
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('util/uploader')+'&nodisplay=1',
			removeUrl: core.getUrl('util/remove')
		});

		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-type"))
			}
		});
	    		
		$('#btnSend').click(function() {
			core.json('steward/suggestrob', {
				id:modal.params.id
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				RhUI.alert(ret.result.message, '', function() {
					location.href = core.getUrl('manage/steward')+'&status=2';
				})				
			}, true, true)
		});
		
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#category_type').isEmpty()) {
				RhUI.toast.show('请选择状态');
				return
			}
			
			if ($('#content').isEmpty()) {
				RhUI.toast.show('内容不能为空');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			var content = $('#content').val();
			var category = $('#category_type').val();
			var suggest_id = $('#suggest_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('steward/suggestreply', {
				'id':suggest_id,
				'status':category,
				'images': images,
				'content': content
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('manage/steward')+'&status=2';
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交申请');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.initTrack = function(params) {
		modal.params = params;		
				
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}						
					
			var suggest_id = $('#suggest_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('steward/suggestfinish', {
				'id':suggest_id
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('service/mysuggest');
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('已处理好');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.initMtrack = function(params) {
		modal.params = params;		
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('util/uploader')+'&nodisplay=1',
			removeUrl: core.getUrl('util/remove')
		});
		
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}						
			if ($('#content').isEmpty()) {
				RhUI.toast.show('内容不能为空');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			var content = $('#content').val();			
			var suggest_id = $('#suggest_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('steward/suggesttrack', {
				'id':suggest_id,
				'images': images,
				'content': content
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('home/index');
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交申请');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	return modal
});