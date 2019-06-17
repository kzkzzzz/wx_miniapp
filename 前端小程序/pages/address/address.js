// pages/address/address.js
const app = getApp();

Page({
  data: {
    address: {
      name: '',
      phone: '',
      address: ''
    },
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    this.setData({
      address: app.globalAddress
    })
  },

  saveContent: function(e) {
    let addr = 'address.' + e.target.dataset.name;
    this.setData({
      [addr]: e.detail.value
    })
  },

  goLocation: function(e) {
    wx.chooseLocation({
      success: res => {
        app.globalAddress.address = res.address
        this.setData({
          address: app.globalAddress
        })
      },
      fail: errmsg => {
        wx.getSetting({
          success: res => {
            if (!res.authSetting['scope.userLocation']) {
              wx.showModal({
                title: '提示',
                content: '定位需要打开位置信息',
                success: ress => {
                  if (ress.confirm) {
                    wx.openSetting({
                      success: resss => {
                        // console.log(resss)
                      }
                    })
                  }
                }
              })
            }
          }
        })
      }
    })
  },


  saveAddress: function(e) {
    app.globalAddress = this.data.address,
      wx.navigateBack({
        detail: 1
      })
  }

})