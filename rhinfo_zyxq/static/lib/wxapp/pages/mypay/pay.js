// pages/pay/pay.js
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
  
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) { 
    app.util.request({
      url: 'entry/site/minimypay',
      data: {
        'm': 'rhinfo_zyxq',
		'regionid':app.siteInfo.regionid,
        'params': options.params,
        'appopenid': app.globalData.userId.openid
      },
      cachetime: 0,
      showLoading: false,
      success: function (res) {
        if (res.data && res.data.result) {
          wx.requestPayment({
            'timeStamp': res.data.result.timeStamp,
            'nonceStr': res.data.result.nonceStr,
            'package': res.data.result.package,
            'signType': res.data.result.signType,
            'paySign': res.data.result.paySign,
            'success': function (res) {
              wx.navigateTo({
                url: '../index/index'
              });
            },
            'fail': function (res) {
              wx.navigateBack();
            }
          })
        }
        else {
          wx.showModal({
            title: '提示', content: res.data.message, success: function (res) {
              wx.navigateBack();
            }
          })
        }
      }
    })
  }
})