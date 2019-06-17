const app = getApp();

Page({

  data: {
    check:false,
    orders:{}
  },

  getOrder:function(){
    let data = { userid: this.data.userid };

    app.asyncRequest('GET', app.globalUrl + '/v1/user/order', data).then(res => {
      wx.stopPullDownRefresh()
      console.log(res.data.body)
      this.setData({
        orders: res.data.body.orders
      })

    }).catch(error => {
      wx.stopPullDownRefresh()
      console.log(error)
    })
  },

  onLoad: function (options) {
    console.log(options.userid)
    this.setData({
      userid: options.userid
    })
    this.getOrder()
  },

  onPullDownRefresh:function(){
    this.getOrder();
  }

})