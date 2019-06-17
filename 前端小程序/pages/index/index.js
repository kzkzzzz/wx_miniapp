const app = getApp()
const cal = require('../../utils/cal.js')
var QQMapWX = require('../../utils/qqmap-wx-jssdk.js');
var qqmapsdk;

Page({
  data: {
    banner: '',
    location: {},
    weather: {},
    hide: true
  },

  previewImage:function(e){
    wx.previewImage({
      current: this.data.banner[e.target.id],
      urls: this.data.banner,
    })
  },

  // 轮播图
  getBanner: function() {
    app.asyncRequest('GET', app.globalUrl + '/banner').then(res => {
      // console.log('banner', res.data.body);
      this.setData({
        banner: res.data.body
      })
    }).catch(error => {
      console.log(error)
    })
  },


  onLoad: function(e) {

    this.getBanner();

    /* 初始化腾讯地图api */
    qqmapsdk = new QQMapWX({
      key: '2FCBZ-OMMLX-GPU4W-TCKTT-BN3R5-5PBWP'
    })

    /* 支付成功重定向页面 */
    if (e.redirect == 'order') {
      this.goOrder()
    }
    this.getWeather();
  },

  /* 打开商店 */
  goShop: function(e) {
    wx.navigateTo({
      url: '/pages/shop/shop',
    })
  },

  /* 查看订单 */
  goOrder: function(e) {
    let header = app.authHeader();
    app.asyncRequest('GET', app.globalUrl + '/v1/user/check', '', header).then(res => {
      wx.navigateTo({
        url: '/pages/order/order?userid=' + res.data.body.userid,
      })
    }).catch(error=>{
      console.log(error)
    });


  },


  /* 获取地址和天气 */
  getAddress: function(latitude, longitude) {
    let that = this;
    qqmapsdk.reverseGeocoder({ //腾讯地图接口经纬度获取省市信息
      location: {
        latitude: latitude,
        longitude: longitude
      },
      success: function(res) {
        that.setData({
          location: {
            province: res.result.ad_info.province,
            city: res.result.ad_info.city,
            district: res.result.ad_info.district
          }
        })
        let data = {
          city: res.result.ad_info.district
        }
        app.asyncRequest('GET', app.globalUrl + '/weather', data).then(res => {
          // console.log(res.data.body)
          that.setData({
            weather: res.data.body,
            hide: false
          })
          wx.stopPullDownRefresh()
        }).catch(error => {
          console.log(error)
          wx.stopPullDownRefresh()
        })

      },
      fail: function(res) {
        console.log(res)

      },
    })
  },

  /* 调用天气方法 */
  getWeather: function() {
    /* wx.getSetting({
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
    }) */

    let errorFn = function() {
      wx.stopPullDownRefresh();
    }

    let data = {
      city: this.data.location.district
    };

    wx.getLocation({
      type: 'gcj02',
      success: res => {
        // console.log(res);
        this.getAddress(res.latitude, res.longitude)
      },
      fail: res => {

        wx.stopPullDownRefresh();
        this.setData({
          hide: false,
          location: {
            province: '定位失败'
          },
          weather: {
            win: '获取天气信息失败'
          }
        })

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

  /* 刷新 */
  onPullDownRefresh: function(e) {
    this.getBanner();
    this.getWeather();
  },


})