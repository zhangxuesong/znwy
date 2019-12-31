define(['core', 'tpl'], function (core, tpl) {
    var modal = {params: {applytitle: '', open_protocol: 0}};
    modal.init = function (params) {
        modal.params = $.extend(modal.params, params || {});

        $('.btn-submit').click(function () {
            var btn = $(this);
            if (btn.attr('stop')) {
                return
            }
            var html = btn.html();
            var diyformdata = false;
            if ($('#title').isEmpty()) {
                RhUI.toast.show('请填写商家称!');
                return
            }
            if ($('#salecate').isEmpty()) {
                RhUI.toast.show('请填写主营项目!');
                return
            }
            if ($('#realname').isEmpty()) {
                RhUI.toast.show('请填写联系人!');
                return
            }

            if ($('#uname').isEmpty()) {
                RhUI.toast.show('请填写帐号!');
                return
            }
            if ($('#upass').isEmpty()) {
                RhUI.toast.show('请填写密码!');
                return
            }
            var data = {
                'realname': $('#realname').val(),
                'mobile': $('#mobile').val(),
                'merchname': $('#merchname').val(),
                'uname': $('#uname').val(),
                'upass': $('#upass').val(),
                'salecate': $('#salecate').val(),
                'desc': $('#desc').val(),
                'mdata': {}
            }

            if ($(".diyform-container").length > 0) {
                diyformdata = diyform.getData('.page-merch-register .diyform-container');
                if (!diyformdata) {
                    return
                }
                data.mdata =  diyformdata;
            }

            if (modal.params.open_protocol == 1) {
                if (!$('#agree').prop('checked')) {
                    RhUI.toast.show('请阅读并了解【'+ modal.params.applytitle +'】!');
                    return
                }
            }

            btn.attr('stop', 1).html('正在处理...');
            core.json('business/register', data, function (pjson) {
                if (pjson.status == 0) {
                    btn.removeAttr('stop').html(html);
                    RhUI.toast.show(pjson.result.message);
                    return
                }

                RhUI.message.show({
                    icon: 'icon icon-info text-warning',
                    content: "您的申请已经提交，请等待我们联系您!",
                    buttons: [{
                        text: '先去商城逛逛', extraClass: 'btn-danger', onclick: function () {
                            location.href = core.getUrl('')
                        }
                    }]
                });

            }, true, true)
        });
        
        $("#btn-apply").unbind('click').click(function () {
            var html = $(".pop-apply-hidden").html();
            container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close();
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });
            $('.verify-pop').find('.btn').unbind('click').click(function () {
                container.close();
            })
        });
    };
    return modal
});