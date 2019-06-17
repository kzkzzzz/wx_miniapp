const app = getApp()
Page({

  data: {

  },

  onLoad: function (options) {

  },

  saveContent: function (e) {
    let key = 'form.' + e.target.dataset.name;
    let value = e.detail.value;
    this.setData({
      [key]: value,
    })
  },


  loginAccount: function (e) {
    console.log(this.data.form);
    let header = {
      "Content-Type": "application/json"
    }
    let data = this.data.form;
    app.asyncRequest('POST', app.globalUrl + '/login', data, header).then(res => {
      console.log(res.data.body)
      if (res.data.body.success) {
        wx.showModal({
          title: '登录成功',
          content: '登录时间 ' + res.data.body.login_time,
          showCancel: false,
          success:function(res){
            if(res.confirm){
              wx.reLaunch({
                url: '/pages/index/index',
              })
            }
          }
        })

        wx.setStorage({
          key: 'loginInfo',
          data: res.data.body.token,
        })

        wx.setStorage({
          key: 'userInfo',
          data: JSON.stringify(res.data.body.userInfo),
        })

        console.log(JSON.stringify(res.data.body.userInfo));

        return
      }
      for (let key in res.data.body) {
        wx.showModal({
          title: '提示',
          content: res.data.body[key][0],
          showCancel: false
        })
        break;
      }
    })
  }

})