const app = getApp();
Page({
  data: {    
    url:app.globalData.siteroot + 'app/index.php?i=' + app.globalData.uniacid + '&c=entry&do=myauth&op=home&m=rhinfo_zyxq',
  },
  onLoad(query) {
    // 页面加载
    var that = this;
    my.getAuthCode({
      scopes: 'auth_base',
      success: (res) => {
        var auth_code = res.authCode;
        my.httpRequest({
          url: app.globalData.siteroot + 'app/miniapi.php?i=' + app.globalData.uniacid + '&c=entry&a=site&do=openid&m=rhinfo_zyxq',
          data: {op:'aliapp',code:auth_code},
          success:(res)=> {
            console.log(res.data);
            var data = res.data;
            if(data.errno==0){
              that.setData({ url: app.globalData.siteroot + 'app/index.php?i=' + app.globalData.uniacid + '&c=entry&do=home&m=rhinfo_zyxq' });
            }
            else{
              that.setData({ url: app.globalData.siteroot + 'app/index.php?i=' + app.globalData.uniacid + '&c=entry&do=myauth&m=rhinfo_zyxq' });
            }
          },
          fail:(res)=>{
               that.setData({ url: app.globalData.siteroot + 'app/index.php?i=' + app.globalData.uniacid + '&c=entry&do=myauth&m=rhinfo_zyxq' });
          }
        });
      },
    });
  },
  onShareAppMessage() {
    // 返回自定义分享信息   
  },
});
