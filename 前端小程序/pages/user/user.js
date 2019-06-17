const app = getApp()
const cal = require('../../utils/cal.js')

Page({
  /**
   * 页面的初始数据
   */
  data: {
    payview: false,
    user: {},
    form: {
      account: '',
      password: '',
    },
  },

  onLoad: function (options) {
    let that = this;
    try{
      let userinfo = wx.getStorageSync('userInfo');
      /* 从用户缓存信息查看有没有ac 有则是用账号登录 如果没有就检查wx.checkSession */
      if (JSON.parse(userinfo).ac) {
        console.log('走的是账号密码登录');
        that.setData({
          user: JSON.parse(userinfo)
        })
      } else{
        console.log('走的是微信登录');
        app.checkLoginSession(that);
      }
      
    } catch(error){
      // console.log(error);//捕捉异常
      app.checkLoginSession(that)
    }
    /* wx.getStorage({
      key: 'userInfo',
      success: function(res) {
      if(JSON.parse(res.data).ac) {
        console.log(123);
        that.setData({
          userInfo:JSON.parse(res.data)
        })
      }
      },
    })

    app.checkLoginSession(that) */
  },

  userLogin: function (localUserinfo={}) {
    let that = this;
    wx.login({
      success: function(res) {
        if (res.code) {
          let header = {
            "Content-Type": "application/json"
          }
          let data = {
            code: res.code,
            userinfo: localUserinfo
          }
          app.asyncRequest('POST', app.globalUrl + '/login', data, header).then(res => {
            console.log(res.data.body);

            wx.setStorage({
              key: 'loginInfo',
              data: res.data.body.token,
            })

            wx.setStorage({
              key: 'userInfo',
              data: JSON.stringify(res.data.body.userInfo)
            })
            that.setData({
              user: res.data.body.userInfo,
            })

            /* if (res.data.code == 200) {
              wx.setStorage({
                key: 'loginInfo',
                data: res.data.body.token,
              })
            } */

          })
        }
      }
    })
  },

  userInfo: function(e) {
    let that = this;
    if (!e.detail.userInfo) {
      wx.showModal({
        title: '登录失败',
        content: '请重新登录授权',
        showCancel: false
      })
    } else {
      
      let localUserinfo = e.detail.userInfo
      that.userLogin(localUserinfo);
 
    }
  },

  bindGetSetting: function(e) {
    wx.checkSession({
      success: function(res) {
        console.log(res)
      },
      fail: function(res) {
        console.log(res)
      }
    })
    wx.getSetting({
      success: function(res) {
        console.log(res)
      }
    })

  },

  goRegister:function(e){
    console.log(e.target.dataset.method)
    wx.navigateTo({
      url: '/pages/register/register?method=' + e.target.dataset.method,
    })
  },
  goLogin:function(){
    wx.navigateTo({
      url: '/pages/login/login'
    })
  },
  goLogout:function(){
    console.log('退出登录，清除信息');
    wx.clearStorage();
    this.setData({
      user:{}
    })
  }

})