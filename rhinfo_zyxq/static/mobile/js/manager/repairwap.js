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
	    
		
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#category_type').isEmpty()) {
				RhUI.toast.show('请选择报修类别');
				return
			}
			
			if ($('#content').isEmpty()) {
				RhUI.toast.show('报修内容不能为空');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			var content = $('#content').val();
			var category = $('#category_type').val();
			
			$(this).html('正在处理...').attr('stop', 1);
			core.json('manager/repair', {
				'rid':modal.params.rid,
				'cid':category,
				'images': images,
				'content': content
			}, function(ret) {
				if (ret.status == 1) {
					RhUI.toast.show(ret.result.msg);
					setTimeout(function(){
						location.href = core.getUrl('manager/repairtrack') + '&rid=' + modal.params.rid + '&id=' + ret.result.id;
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交');
				RhUI.toast.show(ret.result.message)
			}, true, true)
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
			var repair_id = $('#repair_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('manager/repairreply', {
				'rid':modal.params.rid,
				'id':repair_id,
				'status':category,
				'images': images,
				'content': content
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('manage/steward')+'&status=4';
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交申请');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		});
		$('#btnSend').click(function() {
			core.json('manager/repairrob', {
				'id':modal.params.id,'rid':modal.params.rid
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				RhUI.alert(ret.result.message, '', function() {
					location.href = core.getUrl('manage/steward')+'&status=4';
				})				
			}, true, true)
		});
	};
	modal.initTrack = function(params) {
		modal.params = params;		

		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}						
					
			var repair_id = $('#repair_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('manager/repairfinish', {
				'id':repair_id,'rid':modal.params.rid
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('manager/myrepair');
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
			var repair_id = $('#repair_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('manager/repairtrack', {
				'rid':modal.params.rid,
				'id':repair_id,
				'images': images,
				'content': content
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('manager/myrepairp');
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