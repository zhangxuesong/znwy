define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		modal.params = params;	
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			
			if ($('#comment').isEmpty()) {
				RhUI.toast.show('说点什么吧!');
				return
			}
			var comment = $('#comment').val();	
			$(this).html('正在处理...').attr('stop', 1);
			core.json('steward/teamcomm', {
				'rid': modal.params.rid,
				'type': modal.params.type,
				'id': modal.params.teamid,
				'comment': comment				
			}, function(ret) {
				if (ret.status == 0) {
					location.href = core.getUrl('steward/team');
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	return modal
});