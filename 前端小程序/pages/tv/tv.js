// pages/tv/tv.js
const app = getApp();
Page({
  data: {
    page: 1,
    videoList: [],
    keyword: '',
    isSearch: false,
    imgClass:'opac',
    like:{}
  },

  saveContent: function(e) {
    this.setData({
      keyword: e.detail.value
    })
  },


  searchVideo: function() {
    if (!this.data.isSearch) { //判断是否已经在搜索才记录，不然videoList会被搜索空结果覆盖掉
      this.videoList2 = this.data.videoList
    }
    let keyword = this.data.keyword;

    this.setData({
      isSearch: true
    })

    app.asyncRequest('GET', app.globalUrl + '/v1/videos/search', {
      keyword: keyword
    }).then(res => {

      if (res.data.body.length == 0) {
        this.setData({
          videoList: []
        })
        wx.showToast({
          title: '没有内容',
          icon: 'none'
        })
        return
      }

      this.setData({
        videoList: res.data.body
      })


    })
  },

  searchBack: function(e) {
    if (this.videoList2) { //有进行搜索过，this.videoList2才有值 才执行操作
      this.setData({
        keyword: '',
        videoList: this.videoList2,
        isSearch: false
      })
    }
      
  },



  loadList: function(e) {

    let tempvideoList = this.data.videoList
    let data = {
      page: this.data.page
    }

    app.asyncRequest('GET', app.globalUrl + '/v1/videos', data).then(res => {

      if (res.data.body.length == 0) {
        wx.showToast({
          title: '没有更多了',
          icon: 'none',
          duration:800
        })
        return
      }
      tempvideoList.push(...res.data.body);

      this.setData({
        videoList: tempvideoList,
        page:this.data.page+1
      })
      console.log(res.data.body);



      let query = wx.createSelectorQuery();
      query.select('.main').boundingClientRect((rect) => {
  
        this.setData({
          scrollHeight: rect.height + 'px'
        })
      }).exec()


      

    })
  },


  onLoad: function(options) {
    this.loadList()
  },


  onPullDownRefresh: function() {
    console.log(123)
  },

  contiuneList: function(e) { //触底加载
    // console.log(this.data.isSearch)
    if (this.data.isSearch) return false;
    this.loadList()
  },

  scrollEvent: function(e) {
    if (this.data.isSearch) return;
    if (e.detail.scrollTop < -70) {
      this.setData({
        page: 1,
        videoList: [],
      })
      this.loadList();
    }
  },


  onShow:function(e){
    console.log(app);
    this.setData({
      like: app.globalLike
    })
  },


  likeVideo:function(e){
    let header = app.authHeader();
    app.asyncRequest('GET', app.globalUrl + '/v1/user/check', '', header).then(res => {

      // 这个是用来显示爱心颜色，点击会切换颜色
      let check = app.globalLike['like' + e.currentTarget.dataset.vid] = !app.globalLike['like' + e.currentTarget.dataset.vid]

      let f = check ? 1 : 0; //根据check返回的结果，true爱心红色，传递参数 1 到后端，false变回来，传递参数 0 到后端
      let vindex = e.currentTarget.dataset.index //获取当前视频在videoList数组的索引
      let data = { f: f, vid: e.currentTarget.dataset.vid };
      /* console.log(f);
      return; */
      let header = app.authHeader();
      app.asyncRequest('GET', app.globalUrl + '/v1/videos/like', data, header).then(res => {
        switch (res.data.body.status) {
          case 0:
            this.setData({
              like: app.globalLike
            })
            wx.showToast({
              title: res.data.body.msg,
              icon: 'none',
              duration: 1000
            })
            // let strVideoKey = 'videoList[' +vindex+']["thumbs"]';
            this.setData({
              ['videoList[' + vindex + '].thumbs']: this.data.videoList[vindex].thumbs + (check ? 1 : -1)
            });
            // console.log(this.data.videoList)
            break;

          default:
            wx.showToast({
              title: res.data.body.msg,
              icon: 'none',
              duration: 1000
            })
            break;
        }

      }).catch(error => {
        console.log(error.data.code)
        //  console.log(error);
        //这个是用来显示爱心颜色，失败的时候，改回去false, 让爱心图片的状态恢复默认，因为一开始check检查了取反
        app.globalLike['like' + e.currentTarget.dataset.vid] = false;

      })

    }).catch(error => {
      console.log(123);
    });
    
    

  },

  goVideo:function(e){
    // console.log(e)
    let play = this.data.videoList[e.currentTarget.dataset.index];
    wx.navigateTo({
      url: '/pages/video/video?play=' + encodeURIComponent(JSON.stringify(play))
    })
  }


  /* onReachBottom: function() {
    this.loadList()
    console.log(123)
  }, */

})