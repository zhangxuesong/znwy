define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		rid:0
	};
	modal.init = function(params) {		
		modal.rid = params.rid;
		$('.verify-pop').find('.close').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
           });
		$('.verify-pop').find('.follow').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
			core.json('member/follow', {
				rid: modal.rid
			}, function(ret) {	
				if (ret.status == 1) {
					$(".pop-region-hidden").hide();
				}
				RhUI.toast.show(ret.result.message);				
			}, false, true);
        });		
	};
	
	return modal
});