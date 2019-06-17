//app.js
App({
  globalUrl: 'https://api.maplegg.com',
  globalAddress:{
    name:'',
    phone:'',
    address:''
  },
  globalLike:{},

  authHeader:function(){
    try {
      const token = wx.getStorageSync('loginInfo');
      let header = {
        'Authorization': 'Bearer ' + token
      }
      // console.log(123)
      return header;
    }catch(error){
      console.log('authHader',error)
      return {};
    }
  },

  asyncRequest: (method, url, data = {}, header = {}, errorFn) => {
    let that = this;
    return new Promise((resolve, reject) => {
      wx.showNavigationBarLoading();
      wx.request({
        method: method,
        url: url,
        header: header,
        data: data,
        dataType: 'json',
        success: res => {
          wx.hideNavigationBarLoading();
          if (res.data.code && res.data.code != 200) {
            console.log(res.data);
            if(res.data.code == 401){
              var show = '登录已过期，请重新登录'
            } else{
              var show = res.data.body['message'];
            }
            wx.showModal({
              title: res.data.code + ' ' + res.data.errmsg,
              content: show,
              showCancel: false,
              success:function(res){
                res.confirm && errorFn && errorFn()
              }
            })
            reject(res)
            return
          }
          resolve(res);
        },
        fail: errmsg => {
          wx.hideNavigationBarLoading();
          reject(errmsg)
          return;
        }
      })
    })
  },

  checkLoginSession: function(that) {
    let _this = this;
    wx.checkSession({
      success: function() {
        wx.getSetting({
          success: function(res) {
            if (res.authSetting['scope.userInfo'] && wx.getStorageSync('loginInfo')) {
              wx.getStorage({
                key: 'userInfo',
                success: function(res) {
                  that.setData({
                    user: JSON.parse(res.data)
                  })
                  console.log('这个是微信授权的信息');
                  console.log(JSON.parse(res.data))
                },
                fail: function(res) {
                  wx.getUserInfo({
                    success: function(res) {
                      that.setData({
                        user: res.userInfo
                      })
                      wx.setStorage({
                        key: 'userInfo',
                        data: JSON.stringify(res.userInfo)
                      })
                    },
                  })
                }
              })
            } else {
              wx.showModal({
                title: '提示',
                content: '登录授权已过期',
                showCancel: false
              })
            }
          },
        })
      },
      fail: function() {
        /* wx.getStorage({
          key: 'userInfo',
          success: function(res) {
            if(!(JSON.parse(res.data).ac)){
                console.log(123);
                that.userLogin()
            }
          },
          fail:function(res){
            wx.showModal({
              title: '提示',
              content: '登录已过期，请重新登录',
              showCancel: false
            })
          }
        }) */
        wx.showModal({
          title: '提示',
          content: '登录已过期，请重新登录',
          showCancel: false
        })
      }
    })

  }

})