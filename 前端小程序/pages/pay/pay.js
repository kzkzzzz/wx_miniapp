const app = getApp();

Page({
  data: {
    time:'',
    payStatus:'提交请求',
    payCount:'',
    payPlaceholder: '',
    key:'',
    mhidden:true,
    maxHeight:46,
    toggleHide:true,
    payview:true,
    moreText:'展开全部',
    address: app.globalAddress,
  },

  onLoad: function (options) {
    // console.log(options)
    let goods = JSON.parse(decodeURIComponent(options.goods));
    console.log('上页传过来的选购商品对象',goods);
    let price = Number(options.price);
    let disprice = Number(options.disprice);
    let payplaceholder = options.payplaceholder
    let time = options.time

    this.setData({
      totalGoods: goods,
      totalPrice: price,
      discountPrice:disprice,
      payPlaceholder: payplaceholder,
      time:time
    })
    if(Object.keys(goods).length > 3){ //如果商品大于3 显示展开按钮
      this.setData({
        mhidden:false
      })
    }
  },

  writeAddress:function(e){
    wx.navigateTo({
      url: '/pages/address/address',
    })
  },

  onShow:function(e){
    this.setData({
      address: app.globalAddress
    })
    console.log(app.globalAddress)
  },


  stopTouch:function(e){

  },


  showMore:function(e){
    let toggle = this.data.toggleHide;
    this.setData({
      maxHeight: toggle?10000:46,
      toggleHide: toggle?false:true,
      moreText: toggle?'收起':'展开更多'
    })
    console.log(this.data.maxHeight)
  },

  payView: function (e) {
    
    for (let key in this.data.address ){
      if(this.data.address[key] == ''){
        wx.showToast({
          title: '先输入收货信息',
          icon:'none'
        })
        return false
      }
    }

    wx.getStorage({
      key: 'loginInfo',
      success: (res)=> {
        this.setData({
          payview: false,
          request_token:res.data
        })
        wx.getStorage({
          key: 'userInfo',
          success: (res) => {
            this.setData({
              user: JSON.parse(res.data)
            })
          },
        })
      },
      fail:(error)=>{
        wx.showToast({
          title: '先登录',
          icon:"none"
        })
      }
    })

  },

  closePay: function (e) {
    this.setData({
      payview: true
    })
  },
  saveKey:function(e){
    this.data.key = e.detail.value
  },
  
  checkPay:function(e){
    console.log(this.data)

    let sdata = {
      key: this.data.key,
      userid:this.data.user.id,
      goods:this.data.totalGoods,
      price:{
        discountPrice: this.data.discountPrice,
        totalPrice: this.data.totalPrice
      },
      address:this.data.address
    }
    let url = app.globalUrl +'/v1/goods/payment'
    let header = {
      "Content-type": "application/json"
    }

    if (this.data.key == ''){
      wx.showToast({
        title: 'key不能为空',
        icon: 'none'
      })
      return
    }

    if (sdata.key.length < 6){
      wx.showToast({
        title: 'key长度不小于6位',
        icon: 'none'
      })
      return
    }

    let paycount = 1;

    let paying = setInterval(()=>{
      paycount++;
      if(paycount ==5){
        paycount = 1;
      }
      this.setData({
        payStatus: '正在处理',
        payCount: new Array(paycount + 1).join('.')
      })
    },300)


    app.asyncRequest('PUT', url, sdata,header).then((res)=>{
      console.log(res.data.body.status);
      switch (res.data.body.status){
        case 0:
          clearInterval(paying);
          this.setData({
            payStatus: '兑换成功',
            payCount: ''
          })

          wx.showModal({
            title: res.data.body.msg,
            content: '订单号: ' + res.data.body.ordernum,
            showCancel:false,
            success:function(re){
              if(re.confirm){
                wx.reLaunch({
                  url: '/pages/index/index?redirect=order',
                })
              }
            }
          })
          break;

        default:
          clearInterval(paying);
          this.setData({
            payStatus:'提交请求',
            payCount:''
          })
          
          wx.showToast({
            title: res.data.body.msg,
            icon: 'none'
          })
          break;
      }
    }).catch((error)=>{
      console.log(error)
      wx.showToast({
        title: '网络繁忙, 稍后再试',
        icon: 'none'
      })
    })
  }



})