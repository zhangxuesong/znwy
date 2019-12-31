define(['core', 'tpl'], function (core, tpl) {
    var modal = {
		sid:0,
		page:1
	};
    modal.init = function (params) {
		modal.sid = params.sid;
        $('*[data-toggle=qrcode]').unbind('click').click(function() {
			var obj = $(this);
			var qrcode = obj.data('qrcode');
			$("#eqrcode").html("<img src='" + qrcode + "' width='150px;'/>");
			var html = $(".pop-express-hidden").html();
            var container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });  		
		});
		$('*[data-toggle=take]').unbind('click').click(function() {
			var obj = $(this);
			var sn = obj.data('sn');
			var id = obj.data('id');
			RhUI.confirm('取件码' + sn + ', 确认拿到快件?', function() {
				core.json('expressp/takeexpress', {
					orderno: sn,
					expressid:id,
					sid:modal.sid,
					cfrom:1
				}, function(ret) {
					if (ret.status != 1) {
						RhUI.toast.show(ret.result.message)
						return
					}
					RhUI.toast.show(ret.result.message)
					var audio = new Audio("../addons/rhinfo_zyxq/static/mobile/sound/5012.wav");
					audio.play();
					obj.parents(".fui-card").remove();	
				}, true, true)
			})
		});
    };
	return modal
});