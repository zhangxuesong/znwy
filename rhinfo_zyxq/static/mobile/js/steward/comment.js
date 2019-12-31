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
				RhUI.toast.show('说点什么吧');
				return
			}
			
			var comment = $('#comment').val();
							
			$(this).html('正在处理...').attr('stop', 1);
			core.json('steward/comment', {
				'rid': modal.params.rid,
				'comment': comment,
				'stars': modal.params.level
			}, function(ret) {
				if (ret.status == 0) {
					location.href = core.getUrl('steward');
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交评价');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.initRepair = function(params) {
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
				RhUI.toast.show('说点什么吧');
				return
			}
			
			var comment = $('#comment').val();
							
			$(this).html('正在处理...').attr('stop', 1);
			core.json('steward/repaircomment', {
				'repairid': modal.params.repairid,
				'comment': comment,
				'stars': modal.params.level
			}, function(ret) {
				if (ret.status == 0) {
					location.href = core.getUrl('steward');
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交评价');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.initSuggest = function(params) {
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
				RhUI.toast.show('说点什么吧');
				return
			}
			
			var comment = $('#comment').val();
							
			$(this).html('正在处理...').attr('stop', 1);
			core.json('steward/suggestcomment', {
				'suggestid': modal.params.suggestid,
				'comment': comment,
				'stars': modal.params.level
			}, function(ret) {
				if (ret.status == 0) {
					location.href = core.getUrl('steward');
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交评价');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	return modal
});