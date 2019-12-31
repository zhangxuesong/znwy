var app = getApp();
Page({
  data: {
    canIUse: wx.canIUse('web-view'),
    url: ''
  },
  onLoad: function (options) {
    let url = '';
    if (options.url) {
      url = app.util.base64Decode(options.url);
    }
    this.setData({url:url});
  },
  onShareAppMessage: function (res) {
    url = app.util.base64Encode(res.webViewUrl);
    return {
      path: '/pages/webview/index?url=' + url
    }
  }
})