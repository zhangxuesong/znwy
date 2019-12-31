define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, keyword: '',range:0,sorttype:0,cateid:0,rid:0};
    modal.init = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;
        modal.cateid = params.cateid ? params.cateid : 0 ; 
		modal.sorttype = params.sorttype ? params.sorttype : 0 ;		
		modal.range = params.range? params.range : 0 ;
		modal.rid = params.rid? params.rid : 0 ;
		
		if (modal.cateid > 0) {
            $('.sortmenu_cate ul li').each(function(){
                if ($(this).attr('cateid') == modal.cateid) {
                    $('#sortmenu_cate_text').html($(this).attr('text'));
                }
            });
        }


        $(".sortMenu > li").off("click").on("click",function(){
            var menuclass = $(this).attr("data-class");
            if($("."+menuclass+"").css("display")=="none"){
                $(".sortMenu > div").hide();
                $("."+menuclass+"").show();
                $(".sort-mask").show();
            }else{
                $("."+menuclass+"").hide();
                $(".sort-mask").hide();
            }

        });

        $(".sort-mask").off("click").on("click",function(){
            $(this).hide();
            $(".sortMenu > div").hide();
        });

        $('.sortmenu_rule ul li').click(function () {
            modal.range = $(this).attr('range');
            var text = $(this).attr('text');
            $('#sortmenu_rule_text').html(text);
            $('.sortmenu_rule').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            modal.getList()
        });

        $('.sortmenu_cate ul li').click(function () {
            modal.cateid = $(this).attr('cateid');
            var text = $(this).attr('text');
            $('#sortmenu_cate_text').html(text);
            $('.sortmenu_cate').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            modal.getList()
        });

        $('.sortmenu_sort ul li').click(function () {
            modal.sorttype = $(this).attr('sorttype');
            var text = $(this).attr('text');
            $('#sortmenu_sort_text').html(text);
            $('.sortmenu_sort').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            modal.getList()
        });
		
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }
    };
    modal.getList = function () {
            var _this = this;
            core.json('progoods/list', {page: modal.page,rid:modal.rid, keyword: modal.keyword, sortype:modal.sorttype,cateid:modal.cateid,range:modal.range}, function (ret) {
                var result = ret.result;
				if (result.total <= 0) {
					$('.content-empty').show();
					$('.fui-content').infinite('stop')
				} 
				else {
					$('.content-empty').hide();
					$('.fui-content').infinite('init');
					if (result.list.length <= 0 || result.list.length < result.pagesize) {
						$('.fui-content').infinite('stop')					
					}
				}
                modal.page++;
                core.tpl('.container', 'tpl_progoods_list', result, modal.page > 1);
            }, false, true );
    }; 
    modal.initAdd = function (params) {
		 modal.rid = params.rid;
		 $('#btnSubmit').click(function() {
			 if ($('#btnSubmit').attr('stop')) {
				return
			}
			 var cateid = $("#cateid").val();
			 if($("#cateid").isEmpty()){
				 RhUI.toast.show('请选择物品分类');
				return
			 }
			 var title = $("#title").val();
			 if($("#title").isEmpty()){
				 RhUI.toast.show('物品名称不能为空');
				return
			 }
			 var position = $("#position").val();
			 if($("#position").isEmpty()){
				 RhUI.toast.show('安装位置不能为空');
				return
			 }
			 var goodssn = $("#goodssn").val();
			  if($("#goodssn").isEmpty()){
				 RhUI.toast.show('物品名称不能为空');
				return
			 }
			 $('#btnSubmit').html('正在保存...').attr('stop', 1);
			 core.json('progoods/add', {
					rid:modal.rid,
					cateid:cateid,
					title:title,
					position:position,
					goodssn:goodssn,
					brand:$("#brand").val(),
					spec:$("#spec").val(),
					remark:$("#remark").val()
					} , 
					function (ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);
							$('#btnSubmit').html('提交').removeAttr('stop');
							return
						}
						RhUI.alert(ret.result.message, '', function() {					
							location.href = core.getUrl('progoods/list');					
						})
				}, true, true );
		 });
	}
    return modal
});