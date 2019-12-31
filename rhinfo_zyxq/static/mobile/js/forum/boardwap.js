define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		total:0,
		keyword: '',
		bid: 0,
		pid: 0,
		rid: 0
	};
	modal.init = function(params) {	
		modal.textShow();	
        modal.bid = params.bid;				
		$('#btnFollow').click(function() {
			var isfollow = $(this).data('follow') == '1';

			core.json('forum/follow', {
				bid: modal.bid
			}, function(ret) {

				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				var isfollow = ret.result.isfollow;
				if (isfollow) {
					$('#btnFollow').html('<i class="icon icon-check"></i> 已关注').removeClass('btn-warning').addClass('btn-default')
				} else {
					$('#btnFollow').html('<i class="icon icon-add"></i> 关注').removeClass('btn-default').addClass('btn-warning')
				}
				$('#btnFollow').removeAttr('stop')
			}, true, true)
		});

		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList()
			}
		});
		if (modal.page == 1) {
			modal.getList()
		}
		$('.post-func .icon').click(function() {
			$('.post-func .icon').removeClass('selected');
			$(".post-face").hide();
			$(".post-image").hide();
			$(this).addClass('selected');
			if ($(this).hasClass('icon-emoji')) {
				$(".post-face").show()
			} else if ($(this).hasClass('icon-pic')) {
				$(".post-image").show()
			}
		});
		modal.initface({
			class: '.post-face .item',
			input: $('#content')
		});
		
		$('#btnSend').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#title').isEmpty()) {
				RhUI.toast.show('标题没有填写哦~');
				return
			}
			if ($('#content').isEmpty()) {
				RhUI.toast.show('说点什么吧~');
				return
			}
			var images = [];
			$('#cell-images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			$(this).attr('stop', 1);
			core.json('forum/postsubmit', {
				bid: modal.bid,
				title: $("#title").val(),
				content: $('#content').val(),
				images: images
			}, function(ret) {
				$('#btnSend').removeAttr('stop');
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				var msg = ret.result.checked == '1' ? '发表成功!' : '发表成功，请等待审核!';
				RhUI.alert(msg, '提示', function() {
					if (ret.result.checked == '1') {
						$('.empty').hide();
						$('.container').html('');
						$('.infinite-loading').show();
						modal.page = 1;
						modal.getList()
					}
					$("#myimages1").html("");
					$("#myimages2").html("");
					$.router.back()
				})
			}, true, true)
		});
		modal.bindPostEvents();
		modal.bindEvents()
	};
	modal.complain = function() {
		$("#complain-pic").on("click", function() {
			if ($(".complain-image").css("display") == "none") {
				$(".complain-image").show()
			} else {
				$(".complain-image").hide()
			}
		});
		$(".complain-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".complain-type a").removeClass("active");
				$(this).addClass("active");
				$("#complain_type").val($(this).attr("data-type"))
			}
		});
		$('#btnCompSend').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			modal.type = $('#complain_type').val();
			if (modal.type == '') {
				RhUI.toast.show('请选择投诉类别~');
				return
			}
			if ($('#complain_text').isEmpty()) {
				RhUI.toast.show('说点什么吧~');
				return
			}
			var images = [];
			$('.complain-image').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			$(this).attr('stop', 1);
			core.json('forum/complain', {
				id: modal.postid,
				type: modal.type,
				content: $('#complain_text').val(),
				images: images
			}, function(ret) {
				if (ret.status == 0) {
					$('#btnSend').removeAttr('stop');
					RhUI.toast.show(ret.result.message);
					return
				} else {
					var msg = ret.result.checked == '1' ? '投诉提交成功!' : '投诉提交成功，请等待审核!';
					RhUI.alert(msg, '提示', function() {
						if (ret.result.checked == '1' || $('.reply-list-group .reply-list').length <= 0) {
							$('.empty').hide();
							$('.reply-list-group').html('');
							$('.infinite-loading').show();
							modal.page = 1;
							modal.getList()
						}
						$('#content').val('');
						$('.post-func .icon').removeClass('selected');
						$(".post-face").hide();
						$(".post-image").hide();
						$('#btnSend').removeAttr('stop');
						$.router.back()
					})
				}
			}, true, true)
		})
	};
	modal.getList = function() {
		core.html('forum/postlist', {
			page: modal.page,
			bid: modal.bid
		}, function(html) {
			$('.infinite-loading').hide();
			var length1 = $(".container section").length;	
			if (length1== modal.total) {
				$('.fui-content').infinite('stop')
			}
			else{
				if (html=='') {	
					if(length1==0){
						$('.content-empty').show();
					}				
					$('.fui-content').infinite('stop')
				} 
				else {				
					$('.content-empty').hide();
					$('.forum-empty').show();
					$('.fui-content').infinite('init');
					$('.container').append(html);
					var length2 = $(".container section").length;	
					if (length2== modal.total) {
						$('.fui-content').infinite('stop')
					}
					modal.bindEvents();
					modal.textShow();
				}
			}
			modal.page++;					
		})
	};
	modal.link_complain = function() {
		$(document).off("click", ".link-complain").on("click", ".link-complain", function() {
			modal.postid = $(this).attr("data-id");
			core.json('forum/checkPost', {
				postid: modal.postid
			}, function(ret) {
				if (ret.status > 0) {
					var result = ret.result,
						type = '';
					console.log(result);
					if (result.post.pid == 0) {
						type = '话题'
					} else {
						type = '评论'
					}
					$("#complain_type").val('');
					$(".complain-type a").removeClass("active");
					$('#complain_text').val('');
					$(".complain-image ul").html('');
					$('#complain_text').attr('placeholder', '内容 10-1000个字');
					$("#post_member").html(" " + result.post.nickname + " ");
					$(".complain-type-span").html(type);
					$.router.load('#sns-board-complain-page')
				}
			})
		})
	};
	modal.textShow = function() {
		$(".sns-content-info").css({
			"-webkit-box-orient": "",
			"-webkit-line-clamp": "",
			"display": "",
			"max-height": "4rem"
		});
		$(".sns-card-show").html("全文");
		$(".sns-card-list").each(function() {
			$(this).find(".sns-card-show").hide();
			var $divHeight = $(this).find(".sns-content-info");
			var $divHeightSub = $(this).find(".sns-content-info-sub");
			var dheight = $divHeight.height();
			var dheightSub = $divHeightSub.height();
			if (dheightSub > dheight) {
				$(this).find(".sns-card-show").show();
				var text = $(this).find(".sns-card-show").html();
				$divHeight.css({
					"-webkit-box-orient": "vertical",
					"-webkit-line-clamp": "4",
					"display": "-webkit-box",
				});
				$(document).on("click", ".sns-card-show", function() {
					if (text == "全文") {
						$divHeight.css({
							"-webkit-box-orient": "",
							"-webkit-line-clamp": "",
							"display": "",
							"max-height": "" + dheightSub + "px"
						});
						$(this).html("收起");
						text = "收起"
					} else if (text == "收起") {
						$divHeight.css({
							"-webkit-box-orient": "vertical",
							"-webkit-line-clamp": "4",
							"display": "-webkit-box",
							"max-height": "" + dheight + "px"
						});
						$(this).html("全文");
						text = "全文"
					}
				})
			}
		})
	};
	modal.bindEvents = function() {
		$('.like-good').unbind('click').click(function() {
			var link = $(this);
			if ($(this).attr('stop')) {
				return
			}
			var pid = $(this).data('pid');
			$(this).attr('stop');
			core.json('forum/like', {
				bid: modal.bid,
				pid: pid
			}, function(ret) {
				if (ret.status == 0) {
					link.removeAttr('stop');
					RhUI.toast.show(ret.result.message);
					return
				}
				link.removeAttr('stop');
				$(".like-good[data-pid='" + pid + "'] span").html(ret.result.good)
			}, true, true)
		});
	};
	modal.bindPostEvents = function() {
		$('#uploadimages1').uploader({
			uploadUrl: core.getUrl('util/uploader')+'&nodisplay=1',
			removeUrl: core.getUrl('util/remove')
		});
		$('#uploadimages2').uploader({
			uploadUrl: core.getUrl('util/uploader')+'&nodisplay=1',
			removeUrl: core.getUrl('util/remove')
		});
	};
	modal.initList = function(params) {
		modal.rid = params.rid ? params.rid : 0;	
		modal.keyword = params.keyword ? params.keyword : '' ;	
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getBoardList()
			}
		});
		
		if (modal.page == 1) {
			modal.getBoardList()			
		}

	};
	modal.getBoardList = function() {
		core.json('forum/boardlist', {
			page: modal.page,
			keyword: modal.keyword
		}, function(ret) {				
			var result = ret.result;			
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			
			modal.page++;
			core.tpl('.container', 'tpl_board_lists', result, modal.page > 1)
		})
	};
	modal.initface = function(params) {
		$.extend({
			browser: function() {
				var rwebkit = /(webkit)\/([\w.]+)/,
					ropera = /(opera)(?:.*version)?[ \/]([\w.]+)/,
					rmsie = /(msie) ([\w.]+)/,
					rmozilla = /(mozilla)(?:.*? rv:([\w.]+))?/,
					browser = {},
					ua = window.navigator.userAgent,
					browserMatch = uaMatch(ua);
				if (browserMatch.browser) {
					browser[browserMatch.browser] = true;
					browser.version = browserMatch.version
				}
				return {
					browser: browser
				}
			},
		});

		function uaMatch(ua) {
			ua = ua.toLowerCase();
			var match = rwebkit.exec(ua) || ropera.exec(ua) || rmsie.exec(ua) || ua.indexOf("compatible") < 0 && rmozilla.exec(ua) || [];
			return {
				browser: match[1] || "",
				version: match[2] || "0"
			}
		}
		$.extend({
			unselectContents: function() {
				if (window.getSelection) window.getSelection().removeAllRanges();
				else if (document.selection) document.selection.empty()
			}
		});
		$.fn.extend({
			selectContents: function() {
				$(this).each(function(i) {
					var node = this;
					var selection, range, doc, win;
					if ((doc = node.ownerDocument) && (win = doc.defaultView) && typeof win.getSelection != 'undefined' && typeof doc.createRange != 'undefined' && (selection = window.getSelection()) && typeof selection.removeAllRanges != 'undefined') {
						range = doc.createRange();
						range.selectNode(node);
						if (i == 0) {
							selection.removeAllRanges()
						}
						selection.addRange(range)
					} else if (document.body && typeof document.body.createTextRange != 'undefined' && (range = document.body.createTextRange())) {
						range.moveToElementText(node);
						range.select()
					}
				})
			},
			setCaret: function() {
				if (!$.browser.msie) return;
				var initSetCaret = function() {
						var textObj = $(this).get(0);
						textObj.caretPos = document.selection.createRange().duplicate()
					};
				$(this).click(initSetCaret).select(initSetCaret).keyup(initSetCaret)
			},
			insertAtCaret: function(textFeildValue) {
				var textObj = $(this).get(0);
				if (document.all && textObj.createTextRange && textObj.caretPos) {
					var caretPos = textObj.caretPos;
					caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? textFeildValue + '' : textFeildValue
				} else if (textObj.setSelectionRange) {
					var rangeStart = textObj.selectionStart;
					var rangeEnd = textObj.selectionEnd;
					var tempStr1 = textObj.value.substring(0, rangeStart);
					var tempStr2 = textObj.value.substring(rangeEnd);
					textObj.value = tempStr1 + textFeildValue + tempStr2;
					textObj.focus();
					var len = textFeildValue.length;
					textObj.setSelectionRange(rangeStart + len, rangeStart + len);
					textObj.blur()
				} else {
					textObj.value += textFeildValue
				}
			}
		});
		$(params.class).click(function() {
			var face = "[EM" + $(this).data('face') + "]";
			params.input.setCaret();
			params.input.insertAtCaret(face)
		})
	};
	modal.initRelease = function(params) {	
        modal.rid = params.rid;	
		$('.post-func .icon').click(function() {
			$('.post-func .icon').removeClass('selected');
			$(".post-face").hide();
			$(".post-image").hide();
			$(this).addClass('selected');
			if ($(this).hasClass('icon-emoji')) {
				$(".post-face").show()
			} else if ($(this).hasClass('icon-pic')) {
				$(".post-image").show()
			}
		});
		modal.initface({
			class: '.post-face .item',
			input: $('#content')
		});
		
		$('#btnSend').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($("#boardid").isEmpty()) {
				RhUI.toast.show('请选择版块~');
				return
			}
			if ($('#title').isEmpty()) {
				RhUI.toast.show('标题没有填写哦~');
				return
			}
			if ($('#content').isEmpty()) {
				RhUI.toast.show('说点什么吧~');
				return
			}
			var images = [];
			$('#cell-images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
	
			$(this).attr('stop', 1);
			core.json('forum/postsubmit', {
				rid: modal.rid,
				bid: '*',
				boardid:$("#boardid").val(),
				title: $("#title").val(),
				content: $('#content').val(),
				images: images
			}, function(ret) {
				$('#btnSend').removeAttr('stop');
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				var msg = ret.result.checked == '1' ? '发表成功!' : '发表成功，请等待审核!';
				RhUI.alert(msg, '提示', function() {
					if (ret.result.checked == '1') {
						$('.empty').hide();
						$('.container').html('');
						$('.infinite-loading').show();
						modal.page = 1;
						modal.getList()
					}
					$("#myimages1").html("");
					$.router.back()
				})
			}, true, true)
		});
		modal.bindPostEvents();
	};
	modal.initSrelease = function(params) {	
		modal.rid = params.rid;
		modal.bid = params.bid;
		$('.post-func .icon').click(function() {
			$('.post-func .icon').removeClass('selected');
			$(".post-face").hide();
			$(".post-image").hide();
			$(this).addClass('selected');
			if ($(this).hasClass('icon-emoji')) {
				$(".post-face").show()
			} else if ($(this).hasClass('icon-pic')) {
				$(".post-image").show()
			}
		});
		modal.initface({
			class: '.post-face .item',
			input: $('#content')
		});
		
		$('#btnSend').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#title').isEmpty()) {
				RhUI.toast.show('标题没有填写哦~');
				return
			}
			if ($('#content').isEmpty()) {
				RhUI.toast.show('说点什么吧~');
				return
			}
			var images = [];
			$('#cell-images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			$(this).attr('stop', 1);
			core.json('forum/postsubmit', {
				bid: modal.bid,
				title: $("#title").val(),
				content: $('#content').val(),
				images: images
			}, function(ret) {
				$('#btnSend').removeAttr('stop');
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				var msg = ret.result.checked == '1' ? '发表成功!' : '发表成功，请等待审核!';
				RhUI.alert(msg, '提示', function() {
					if (ret.result.checked == '1') {
						$('.empty').hide();
						$('.container').html('');
						$('.infinite-loading').show();
						modal.page = 1;
						modal.getList()
					}
					$("#myimages1").html("");
					location.href = core.getUrl('home') + '&rid=' + modal.rid;
				})
			}, true, true)
		});
		modal.bindPostEvents();
	};
	return modal
});