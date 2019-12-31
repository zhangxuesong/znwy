define(['core'], function(core) {
	var modal = {};
	modal.initBind = function(params) {
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}			
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			core.json('business/audit', {
				id: $('#creditid').val(),
				reason:''
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('审核通过').removeAttr('stop');
					return
				}				
				RhUI.alert('审核完成!', '', function() {
					location.href = core.getUrl('business/mindex') + '&id=' + $('#creditid').val();
				})
			}, true, true)
		});
		$('#btnSubmitno').click(function() {
			if ($('#btnSubmitno').attr('stop')) {
				return
			}			
			$('#btnSubmitno').html('正在提交...').attr('stop', 1);
			core.json('business/audit', {
				id: $('#creditoid').val(),
				reason:$('#reason').val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('提交').removeAttr('stop');
					return
				}				
				RhUI.alert('审核完成!', '', function() {
					location.href = core.getUrl('business/mindex') '&id=' + $('#creditoid').val();
				})
			}, true, true)
		});
	};
	return modal
});