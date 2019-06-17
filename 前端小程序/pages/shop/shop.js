//index.js
//获取应用实例
const app = getApp();
const util = require('../../utils/util.js');
const cal = require('../../utils/cal.js');

Page({
  data: {
    nav: [
      '菜单',
      '评价',
      '商家'
    ],
    wait: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15],
    waitStatus: true,
    showStatus: false,
    fxview: true,
    fixedScrollH: 0,
    cloading: false,
    currentSwiper: 0,
    hideBottom: false,
    currentNav: 0,
    carScrollH: 0,
    carViewH: 0,
    optmask: false,
    totalGoods: {},
    totalNum: 0,
    totalPrice: 0,
    discountPrice: 0,
    cid: 'c0',
    nowid: 0,
    scrolly: true,
    scrollTopL: 0,
    showData: {}
  },

  onLoad: function() {
    let that = this;

    app.asyncRequest('GET', app.globalUrl+'/v1/goods').then(res => {
      that.setData({
        showData: res.data.body,
        waitStatus: false,
        showStatus: true,
      })
      console.log(res.data)
      wx.setNavigationBarTitle({
        title: res.data.body.shopinfo.name,
      })

      const query = wx.createSelectorQuery();
      let tops = [];
      let topViewH, navViewH, scrollHeight, fixedCateH;
      query.selectAll('.top,.nav,.mid').boundingClientRect(function(heights) {
        topViewH = heights[0].height
        navViewH = heights[1].height
        scrollHeight = heights[2].height
      }).exec()

      query.selectAll('.good-title').boundingClientRect(function(rects) {
        rects.forEach(function(rect, index) {
          //减去顶部固定占位置view的高度
          tops[index] = Math.round(rect.top) - topViewH - navViewH
        })
        that.setData({
          tops: tops
        })
      }).exec()

      query.select('.mid').boundingClientRect(function(mid) {
        that.setData({
          scrollHeight: scrollHeight + 'px'
        })
      }).exec()

    }).catch((errmsg) => {
      wx.showToast({
        title: '网络繁忙，请重试',
        icon: 'none'
      })
    })


  },

  scrollEventL: function(e) {
    // console.log(e.detail.scrollTop)
  },

  scrollEventR: function(e) {
    let topss = this.data.tops;
    let scrollH = e.detail.scrollTop;
    // console.log(scrollH)
    let that = this;
    if (that.data.lastcate == true) {
      that.setData({
        lastcate: false,
        nowid: topss.length - 1
      })
      return
    }
    topss.forEach(function(top, index, tops) {
      if (tops[index] - 15 < scrollH && scrollH <= tops[index + 1]) {
        that.setData({
          nowid: index
        })

        if (index == that.data.nowid && index >= 5) {
          that.setData({
            scrollTopL: 68 * (index - 4) //到中间的分类就开始滚动
          })
        } else if (index < 5) {
          that.setData({
            scrollTopL: 0
          })
        }

      } else if (scrollH > tops[tops.length - 1] - 15) {
        that.setData({
          nowid: tops.length - 1
        })
      }
    })


  },
  scrollToView: function(e) {
    if (e.currentTarget.id == this.data.tops.length - 1) {
      this.setData({
        lastcate: true
      })
    } else {
      this.setData({
        lastcate: false
      })
    }
    this.setData({
      cid: e.currentTarget.dataset.cid,
      nowid: e.currentTarget.id
    })
  },

  stopTouch: function(e) {
    // console.log('空方法')
  },

  //加号操作
  addGood: function(e) {
    let name = 'good' + e.currentTarget.dataset.gid;

    if (!this.data.totalGoods[name]) { //如果此商品不存在 才在购物车加一栏(高度8vh)
      this.setData({
        carScrollH: this.data.carScrollH += 8
      })
    }

    let obj = {
      total: this.data.totalGoods[name] ? this.data.totalGoods[name]['total'] += 1 : 1,
      gprice: e.currentTarget.dataset.gprice,
      disprice: e.currentTarget.dataset.disprice,
      gname: e.currentTarget.dataset.gname,
      gid: e.currentTarget.dataset.gid,
      gimage: e.currentTarget.dataset.gimage
    }
    //优惠的差价 = 原价 - 现价
    //引入cal.js封装的方法计算小数点
    let sprice = cal.calculate.cSub(obj.disprice, obj.gprice)
    this.setData({
      ['totalGoods.' + name + '']: obj,
      totalPrice: cal.calculate.cPlus(this.data.totalPrice, obj.gprice),
      //每点一次加号，优惠的总价格 加上 当前商品的优惠价格
      discountPrice: cal.calculate.cPlus(this.data.discountPrice, sprice),
      totalNum: this.data.totalNum += 1,
    })
    console.log('优惠的总价',this.data.discountPrice)

    /*     console.log(this.data.totalGoods)
        console.log(JSON.parse(this.data.totalGoods)) */

  },

  //减号操作
  subGood: function(e) {
    let name = 'good' + e.currentTarget.dataset.gid;

    let obj = {
      total: this.data.totalGoods[name]['total'] -= 1,
      gprice: e.currentTarget.dataset.gprice,
      disprice: e.currentTarget.dataset.disprice,
      gname: e.currentTarget.dataset.gname,
      gid: e.currentTarget.dataset.gid,
      gimage: e.currentTarget.dataset.gimage
    }
    //优惠的差价 = 原价 - 现价
    //引入cal.js封装的方法计算小数点
    let sprice = cal.calculate.cSub(obj.disprice, obj.gprice)

    this.setData({
      ['totalGoods.' + name + '']: obj,
      totalPrice: cal.calculate.cSub(this.data.totalPrice, obj.gprice),
      //每点一次减号，优惠的总价格 减去 当前商品的优惠价格
      discountPrice: cal.calculate.cSub(this.data.discountPrice, sprice),
      totalNum: this.data.totalNum -= 1,
    })
    console.log(this.data.discountPrice)

    //当前商品数量为0才开始操作购物车高度
    if (this.data.totalGoods[name]['total'] == 0) {
      delete this.data.totalGoods[name]
      //数量0的时候删除商品对象，需要重新设置setdata wxml才能渲染
      let newTotalGoods = this.data.totalGoods
      let oldCarScrollH = this.data.carScrollH
      let oldCarViewH = this.data.carScrollH + 4

      this.setData({
        totalGoods: newTotalGoods,
        carScrollH: oldCarScrollH -= 8, //减去购物车一栏高度8vh
      })

      //carViewH大于0 即 购物车展开的时候才进行购物车高度操
      if (this.data.carViewH > 0) {
        //取scoll-view高度加4是清空购物车占的位置
        this.setData({
          carViewH: oldCarViewH -= 8
        })
      }
      //如果商品都清空了，且是在购物车展开状态(高度大于0) 购物车高度缩小到0
      if (this.data.carScrollH == 0 && this.data.carViewH > 0) {
        // console.log(this.data.carScrollH)
        this.setData({
          carViewH: 0,
          optmask: this.data.optmask ? false : true,
          scrolly: this.data.scrolly ? false : true
        })
        return false
      }


    }
    // console.log(this.data.totalPrice)
  },

  createOrder: function(e) {
    if (this.data.totalNum == 0) {
      wx.showToast({
        title: '未选择商品',
        icon: 'none'
      })
      return false
    }
    /* wx.showToast({
      title: '订单系统开发中......',
      icon: 'none'c
    }) */
    let that = this;
    that.setData({
      cloading: true
    })

    app.asyncRequest('GET', app.globalUrl +'/v1/goods/checkserver').then((res) => {

      that.setData({
        cloading: false,
      })

      if (res.data.body.status == 0) {
        let goods = encodeURIComponent(JSON.stringify(that.data.totalGoods))
        let price = that.data.totalPrice
        let disprice = that.data.discountPrice
        let ordernum = that.data.orderNumber
        wx.navigateTo({
          url: '/pages/pay/pay?goods=' + goods + '&price=' + price + '&disprice=' + disprice + '&payplaceholder=' + res.data.body.payPlaceholder + '&time=' + res.data.body.time
        })
      } else {
        wx.showToast({
          title: '订单系统维护中...',
          icon: 'none'
        })
      }
    }).catch((errmsg) => {
      that.setData({
        cloading: false
      })
      /* wx.showToast({
        title: '网络繁忙，请重试',
        icon: 'none'
      }) */
    })

  },
  showShopCar: function(e) {
    if (this.data.totalNum == 0) { //没添加商品 不执行展开购物车操作
      return false;
    }
    // console.log(this.data.carViewH)
    this.setData({
      optmask: this.data.optmask ? false : true,
      scrolly: this.data.scrolly ? false : true,
      carViewH: !!this.data.carViewH ? 0 : this.data.carScrollH + 4,
    })
  },
  clearShopCar: function(e) {
    let clearobj = {}
    this.setData({
      totalGoods: clearobj,
      totalNum: 0,
      totalPrice: 0,
      optmask: false,
      scrolly: true,
      carViewH: 0,
      carScrollH: 0
    })
  },

  swiperChange: function(e) {
    this.setData({
      currentSwiper: e.detail.current,
      hideBottom: e.detail.current > 0 ? true : false
    })
  },
  manualSwiper: function(e) {
    this.setData({
      currentSwiper: e.currentTarget.dataset.sid
    })
  },
  showFxview: function(e) {
    let that = this;
    let gid = e.currentTarget.dataset.gid;
    if (that.data.fxview) {
      app.asyncRequest('GET', app.globalUrl + '/v1/goods/'+gid).then((res) => {
        that.setData({
          fxview: false,
          fixedScrollH: 0,
          goodDetail: {
            gname: res.data.body.gname,
            gprice: res.data.body.gprice,
            disprice: res.data.body.disprice,
            gdesc: res.data.body.gdesc,
            gsale: res.data.body.gsale,
            gimage: res.data.body.gimage
          }
        })
      }).catch((errmsg) => {
        wx.showToast({
          title: '网络繁忙，请重试',
          icon: 'none'
        })
      })
    } else {
      that.setData({
        fxview: true,
        fixedScrollH: 0
      })
    }

  }



})