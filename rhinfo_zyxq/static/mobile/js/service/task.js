define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1,taskid:0,parentid:0};
    modal.init = function (params) {
        modal.page = 1;
		modal.taskid = params.taskid;
		modal.parentid = params.parentid;
 
        $(".container").empty();
        modal.getList()

        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
    };
    modal.getList = function () {
        core.json('task/taskinfo', {id: modal.taskid, page:modal.page,parentid:modal.parentid}, function (ret) {
            var result = ret.result;
            if (result.total <= 0) {
                $('.content-empty').show();
                $('.fui-content').infinite('stop')
            } else {
                $(".container").show();
                $('.content-empty').hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop')
                }
            }
            modal.page++;
            core.tpl('.container', 'record_tpl', result, modal.page > 1)
        })
    };
    return modal
});