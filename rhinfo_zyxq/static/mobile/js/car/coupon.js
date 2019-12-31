define(['core','tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {	
		$('#btnSubmit').click(function(){
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if ($('#carno').isEmpty()) {
			   RhUI.toast.show('车牌不能为空');
			   return
			}
			if ($('#qty').isEmpty()) {
			   RhUI.toast.show('数量不能为空');
			   return
			}
			var ctype = $.trim($("input[name=ctype]:checked").val());	
			var parkid = $.trim($("input[name=parkid]:checked").val());
			var carno = $("#carno").val();
			$('#btnSubmit').html('正在提交...').attr('stop', 1);
			core.json('car/coupon',{
				 parkid:parkid,
				 carno:carno,
				 qty:$("#qty").val(),
				 ctype:ctype
			}, function(rjson) {
				if (rjson.status != 1) {					
					RhUI.toast.show(rjson.result.message);
					$('#btnSubmit').html('<i class="icon icon-discount"></i> 领优惠券').removeAttr('stop');
					return
				}
				RhUI.toast.show(rjson.result.message);
				location.href = core.getUrl('car/pay') + "&carno=" + carno + "&parkid=" + parkid;
			})
		})
	};
	return modal
});