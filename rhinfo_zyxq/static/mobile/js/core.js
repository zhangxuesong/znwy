define(['jquery', 'rhui', 'tpl'], function ($, RhUI, tpl) {
    window.RhUI = RhUI;
    var defaults = {baseUrl: '', siteUrl: '', staticUrl: '../addons/rhinfo_zyxq/static/mobile/'};
    var core = {options: {}};
    core.init = function (options) {
        this.options = $.extend({}, defaults, options || {})
    };
    core.toQueryPair = function (key, value) {
        if (typeof value == 'undefined') {
            return key
        }
        return key + '=' + encodeURIComponent(value === null ? '' : String(value))
    };
    core.number_format = function (number, fix) {
        var fix = arguments[1] ? arguments[1] : 2;
        var fh = ',';
        var jg = 3;
        var str = '';
        number = number.toFixed(fix);
        zsw = number.split('.')[0];
        xsw = number.split('.')[1];
        zswarr = zsw.split('');
        for (var i = 1; i <= zswarr.length; i++) {
            str = zswarr[zswarr.length - i] + str;
            if (i % jg == 0) {
                str = fh + str;
            }
        }
        str = (zsw.length % jg == 0) ? str.substr(1) : str;
        zsw = str + '.' + xsw;
        return zsw
    };
    core.toQueryString = function (obj) {
        var ret = [];
        for (var key in obj) {
            key = encodeURIComponent(key);
            var values = obj[key];
            if (values && values.constructor == Array) {
                var queryValues = [];
                for (var i = 0, len = values.length, value; i < len; i++) {
                    value = values[i];
                    queryValues.push(this.toQueryPair(key, value))
                }
                ret = ret.concat(queryValues)
            } else {
                ret.push(this.toQueryPair(key, values))
            }
        }
        return ret.join('&')
    };
    core.getUrl = function (routes, params, full) {        		
		routes = routes.replace(/\//ig, "&op=");
		routes = '&do=' + routes;
        var url = this.options.baseUrl.replace('ROUTES', routes);
        if (params) {
            if (typeof(params) == 'object') {
                url += "&" + this.toQueryString(params)
            } else if (typeof(params) == 'string') {
                url += "&" + params
            }
        }
        return full ? this.options.siteUrl + 'app/' + url : url
    };
    core.json = function (routes, args, callback, hasloading, ispost) {
        var url = ispost ? this.getUrl(routes) : this.getUrl(routes, args);
        var op = {
            url: url, type: ispost ? 'post' : 'get', dataType: 'json', cache: false, beforeSend: function () {
                if (hasloading) {
                    RhUI.loader.show('mini')
                }
            }, error: function (a) {
            //    alert(JSON.stringify(a));
                if (hasloading) {
                    RhUI.loader.hide()
                }
            }
        };
        if (args && ispost) {
            op.data = args
        }
        if (callback) {
            op.success = function (data) {
                if (hasloading) {
                    RhUI.loader.hide()
                }
                callback(data)
            }
        }
        $.ajax(op)
    };
    core.post = function (routes, args, callback, hasloading) {
        this.json(routes, args, callback, hasloading, true)
    };
    core.html = function (routes, args, callback, hasloading, async) {
        if (typeof async === undefined) {
            async = true
        }
        var op = {
            url: this.getUrl(routes, args),
            type: 'get',
            cache: false,
            dataType: 'html',
            async: async,
            beforeSend: function () {
                if (hasloading) {
                    RhUI.loader.show('mini')
                }
            },
            error: function () {
                core.removeLoading();
                if (hasloading) {
                    RhUI.loader.hide()
                }
            }
        };
        if (callback) {
            op.success = function (html) {
                if (hasloading) {
                    RhUI.loader.hide()
                }
                callback(html)
            }
        }
        $.ajax(op)
    };
    core.tpl = function (containerid, templateid, data, append) {
        if (typeof append === undefined) {
            append = false
        }
        var html = tpl(templateid, data);
        if (append) {
            $(containerid).append(html)
        } else {
            $(containerid).html(html)
        }
        setTimeout(function () {
            $(containerid).closest('.fui-content').lazyload('render')
        }, 10)
    };
    core.getNumber = function (str) {
        str = $.trim(str);
        if (str == '') {
            return 0
        }
        return parseFloat(str.replace(',', ''))
    };
    core.showIframe = function (url) {
        var if_w = "100%";
        var if_h = $(document.body).height();
        $("<iframe width='" + if_w + "' height='" + if_h + "' id='mainFrame' name='mainFrame' style='position:absolute;z-index:4;'  frameborder='no' marginheight='0' marginwidth='0' ></iframe>").prependTo('body');
        var st = document.documentElement.scrollTop || document.body.scrollTop;
        var sl = document.documentElement.scrollLeft || document.body.scrollLeft;
        var ch = document.documentElement.clientHeight;
        var cw = document.documentElement.clientWidth;
        var objH = $("#mainFrame").height();
        var objW = $("#mainFrame").width();
        var objT = Number(st) + (Number(ch) - Number(objH)) / 2;
        var objL = Number(sl) + (Number(cw) - Number(objW)) / 2;
        $("#mainFrame").css('left', objL);
        $("#mainFrame").css('top', objT);
        $("#mainFrame").attr("src", url)
    };
    core.getDistanceByLnglat = function (lng1, lat1, lng2, lat2) {
        function rad(d) {
            return d * Math.PI / 180.0
        }

        var rad1 = rad(lat1), rad2 = rad(lat2);
        var a = rad1 - rad2, b = rad(lng1) - rad(lng2);
        var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(rad1) * Math.cos(rad2) * Math.pow(Math.sin(b / 2), 2)));
        s = s * 6378137.0;
        s = Math.round(s * 10000) / 10000000;
        return s
    };
    core.showImages = function(imgClass){
        var ua = navigator.userAgent.toLowerCase();
        var isWX = ua.match(/MicroMessenger/i) == "micromessenger";
        var z = [];
        $(imgClass).each(function() {
            var img = $(this).attr("data-lazy");
            z.push(img?img:$(this).attr("src"));
        });
        var current;
        if (isWX) {
            $(imgClass).unbind('click').click(function(e) {
                e.preventDefault();
                var startingIndex = $(imgClass).index($(e.currentTarget));
                var current = null;
                $(imgClass).each(function(B, A) {
                    if (B === startingIndex) {
                        current = $(A).attr("data-lazy")?$(A).attr("data-lazy"):$(A).attr("src");
                    }
                });
                WeixinJSBridge.invoke("imagePreview", {
                    current: current,
                    urls: z
                });
            });
        }
    };
    core.ish5app = function () {
        var userAgent = navigator.userAgent;
        if (userAgent.indexOf('CK 2.0') > -1){
            return true;
        }
        return false;
    };
    core.isWeixin = function () {
        var ua = navigator.userAgent.toLowerCase();
        var isWX = ua.match(/MicroMessenger/i) == "micromessenger";
        return isWX;
    };
	core.wxUploadImages = function(obj,myimages,uploadUrl,removeUrl){
		var max = obj.data('max');		
		var count = $("#" + myimages + " li").length;		
		var images = {
			localIds: [],
		};
        var ua = navigator.userAgent.toLowerCase();
        var isWX = ua.match(/MicroMessenger/i) == "micromessenger";
        if (isWX) {			
            wx.chooseImage({
				count: max - count, // 最多选几张
				sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
				sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
				success: function(res) {
					images.localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
					var i = 0; var length = images.localIds.length;
					var upload = function() {
						wx.uploadImage({
							localId:'' + images.localIds[i],
							isShowProgressTips: 1,
							success: function(res) {
								var serverId = res.serverId;									
								$.ajax({   
									 url:uploadUrl,   
									 type:'post', 
									 data:{
										media_id:serverId,
									 },
									 dataType:'json',
									 success:function(data){ 										
										if (data.error == 1) {
											RhUI.toast.show(data.message);
										} else {
											var removeHTML = "";
											  removeHTML += '<span class="image-remove">';
											  removeHTML += "<i class='icon icon-roundclose'></i>";
											  removeHTML += '</span>';												
											 var imageHTML = '<li style="background-image:url(\'' + data.imgurl + '\')" class="image image-sm" data-filename="' + data.realimgurl + '">' + removeHTML + '</li>';
											
											$("#" + myimages).append(imageHTML);
											count++;
											if(max<=count){
												obj.hide();
											}
											$('.image-remove').unbind('click').click(function () {												
												core.removeMyImages($(this),removeUrl);				
											});
										}  
									 }
								});
								//如果还有照片，继续上传
								i++;
								if (i < length) {
									upload();
								}
							}
						});                    
					};
					upload();
				}
			}); 
        }
    };
	core.wxUploadCamera = function(obj,myimages,uploadUrl,removeUrl){
		var max = obj.data('max');		
		var count = $("#" + myimages + " li").length;		
		var images = {
			localIds: [],
		};
        var ua = navigator.userAgent.toLowerCase();
        var isWX = ua.match(/MicroMessenger/i) == "micromessenger";
        if (isWX) {			
            wx.chooseImage({
				count: max - count, // 最多选几张
				sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
				sourceType: ['camera'], // 可以指定来源是相册还是相机，默认二者都有
				success: function(res) {
					images.localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
					var i = 0; var length = images.localIds.length;
					var upload = function() {
						wx.uploadImage({
							localId:'' + images.localIds[i],
							isShowProgressTips: 1,
							success: function(res) {
								var serverId = res.serverId;									
								$.ajax({   
									 url:uploadUrl,   
									 type:'post', 
									 data:{
										media_id:serverId,
									 },
									 dataType:'json',
									 success:function(data){ 										
										if (data.error == 1) {
											RhUI.toast.show(data.message);
										} else {
											var removeHTML = "";
											  removeHTML += '<span class="image-remove">';
											  removeHTML += "<i class='icon icon-roundclose'></i>";
											  removeHTML += '</span>';												
											var imageHTML = '<li style="background-image:url(\'' + data.imgurl + '\')" class="image image-sm" data-filename="' + data.realimgurl + '">' + removeHTML + '</li>';
											
											$("#" + myimages).append(imageHTML);
											count++;
											if(max<=count){
												obj.hide();
											}
											$('.image-remove').unbind('click').click(function () {												
												core.removeMyImages($(this),removeUrl);				
											});
										}  
									 }
								});
								//如果还有照片，继续上传
								i++;
								if (i < length) {
									upload();
								}
							}
						});                    
					};
					upload();
				}
			}); 
        }
    };

	core.removeMyImages = function(obj,removeUrl){
       RhUI.loader.show('mini');
		var item = obj.closest('li');				
		$.ajax({
			url: removeUrl,
			type: 'post',
			dataType: 'json',
			data: {filename: item.data('filename')},
			cache: false,
			success: function (ret) {
				RhUI.loader.hide();
				if (ret.status == 'success') {
					item.remove();					
					$('.fui-uploader').show();					
				} else {
					RhUI.toast.show(ret.message);
				}

			}
		});
    };
	
    window.core = core;
    return core
});