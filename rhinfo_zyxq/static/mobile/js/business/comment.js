define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		modal.params = params;
		modal.params.level = 0;
		$('.fui-stars').stars({
			'icon': 'icon icon-favor',
			'selectedIcon': 'icon icon-favorfill',
			'onSelected': function(value) {
				modal.params.level = value
			}
		});	

		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if (modal.params.iscomment == 0 && modal.params.level < 1) {
				RhUI.toast.show('还没有评分');
				return
			}
			if ($('#comment').isEmpty()) {
				RhUI.toast.show('说点什么吧!');
				return
			}
			
			var comment = $('#comment').val();
			var ischecked=$('#noname').is(':checked');	
						
			$(this).html('正在处理...').attr('stop', 1);
			core.json('business/addcomment', {
				'id': modal.params.id,
				'comment': comment,
				'stars': modal.params.level,
				'noname':ischecked
			}, function(ret) {
				if (ret.status == 1) {
					location.href = core.getUrl('business/detail') + '&id=' + modal.params.id;
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交评价');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	return modal
});