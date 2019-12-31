define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function() {
		$('.btn-edit').unbind('click').click(function() {
			modal.changeMode()
		});
		$('.editcheckall').unbind('click').click(function() {
			var checked = $(this).find(':checkbox').prop('checked');
			$(".edit-item").prop('checked', checked);
			modal.caculateEdit()
		});
		$('.btn-delete').unbind('click').click(function() {
			if ($('.edit-item:checked').length <= 0) {
				return
			}
			modal.remove()
		})
	};
	modal.caculateEdit = function() {
		$('.editcheckall .fui-radio').prop('checked', $('.edit-item').length == $('.edit-item:checked').length);
		var selects = $('.edit-item:checked').length;
		if (selects > 0) {
			$('.btn-delete').removeClass('disabled')
		} else {
			$('.btn-delete').addClass('disabled')
		}
	};
	modal.changeMode = function() {
		if ($('.goods-item').length <= 0) {
			$('.container').hide();
			$('.btn-edit').html('');
			$('.editmode').html('');
			$('.content-empty').show();
			return
		}
		if (modal.status == 'favorite') {
			$('.fui-content').addClass('navbar');
			$('.edit-item').prop('checked', false);
			$('.editcheckall').prop('checked', false);
			$('.editmode').show();
			modal.status = 'edit';
			$('.btn-edit').html('完成')
		} else {
			$('.fui-content').removeClass('navbar');
			$('.editmode').hide();
			modal.status = 'favorite';
			$('.btn-edit').html('编辑')
		}
	};

	modal.remove = function() {
		var ids = [];
		$('.edit-item:checked').each(function() {
			var id = $(this).closest('.goods-item').data('id');
			ids.push(id)
		});
		
		if (ids.length <= 0) {
			return
		}
		RhUI.confirm('确认取消关注吗?', function() {
			core.json('member/removemember', {
				ids: ids
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result);
					return
				}
				$.each(ids, function() {
					$(".goods-item[data-id='" + this + "']").remove()
				});
				modal.changeMode()
			}, true, true)
		})
	};
	return modal
});