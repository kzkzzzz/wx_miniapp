// pages/video/video.js
const app = getApp();
Page({
  data: {
    ccontent:'',//评论输入框的value值
    play:{},
    nav:['简介','评论'],
    scrollHeight:0,
    cm_scrollHeight: 0,//评论scrollView的高度
    wr_height: 0,//评论输入框的高度
    currentSwiper:0,
    tv:{
      controls:false,
      centerbtn:false
    },
    recommend:{}
  },


  onLoad: function (options) {
    let query = wx.createSelectorQuery(); 
    let winHeight = wx.getSystemInfoSync().windowHeight;
    let subTops = 0;
    query.selectAll('.play,.nav').boundingClientRect(rects=>{
      rects.forEach(res=>{
        subTops +=Math.round(res.height)
        
      })
      
      this.setData({
        scrollHeight: winHeight - subTops,
        cm_scrollHeight: Math.round((winHeight - subTops)*0.9),
        wr_height: Math.round((winHeight - subTops) * 0.1)
      })
    }).exec()

    // 上页传过来的对象，先解码
    this.setData({
      play: JSON.parse(decodeURIComponent(options.play))
    })

    // console.log(this.data.play)


    app.asyncRequest('GET', app.globalUrl +'/v1/videos/recommend',{id:this.data.play.id}).then(res=>{
      this.setData({
        recommend: res.data.body
      })
      // console.log(res.data.body)
    }).catch(error=>{
      console.log(error)
    })

    app.asyncRequest('GET', app.globalUrl + '/v1/videos/vcomment', { vid: this.data.play.id }).then(res => {
      this.setData({
        ['play.comments']: res.data.body.comments,
        ['play.comment_num']: res.data.body.comment_num
      })
      console.log(res.data.body)
      console.log(this.data.play)
    }).catch(error => {
      console.log(error)
    })
  

    this.VideoContext = wx.createVideoContext('tv', this);

    setTimeout(()=>{
      this.VideoContext.play()
    },500)

  },

  controlsTrue:function(e){
    if(!this.data.tv.controls){
      this.setData({
        ['tv.controls']: true
      })
    }
  },

  manualChange:function(e){
    this.setData({
      currentSwiper: e.target.dataset.sid
    })
  },

  swiperChange:function(e){
    this.setData({
      currentSwiper:e.detail.current
    })
  },

  redirectVideo:function(e){
    let vindex = e.currentTarget.dataset.vindex;

    let play = encodeURIComponent(JSON.stringify(this.data.recommend[vindex]));

    wx.navigateTo({
      url: '/pages/video/video?play='+play,
    })
  },

  saveComment:function(e){
      this.setData({
        ccontent:e.detail.value
      })
  },

  sendComment:function(e){
    wx.getStorage({
      key: 'userInfo',
      success: res=>{
        // console.log(JSON.parse(res.data))
        let header = app.authHeader();
        let data = {
          vid: this.data.play.id,
          uid:JSON.parse(res.data).id,
          content:this.data.ccontent
        };
        /* console.log(data);
        return; */
        app.asyncRequest('PUT', app.globalUrl + '/v1/videos/savecomment', data, header).then(res => {
          switch(res.data.body.status){
            case 0:
            let comments = this.data.play.comments;
            comments.unshift(res.data.body.new_comment)
  
            this.setData({
              ccontent:'',
              ['play.comments']: comments,
              ['play.comment_num']: this.data.play.comment_num +1
            })

            wx.showToast({
                title: res.data.body.msg,
                icon: 'none',
                duration: 1000
            })

            break;

            case 1:
            for(let key in res.data.body.errors){
              wx.showToast({
                title: res.data.body.errors[key][0],
                icon:'none',
                duration:800,
              })
              break;
            }
            break;

            case 2:
              wx.showToast({
                title: res.data.body.msg,
                icon: 'none',
                duration: 1000
              })

          }
        })
      },
      fail:error=>{
          wx.showToast({
            title: '未登录',
            icon:'none',
            duration:800
          })
        return false
      }
    })

    // console.log(this.data.ccontent)
  },

  recordPlay:function(e){
    /* console.log(this.data.play);
    return */
    let header = {
      Recordvid : this.data.play.id
    }
    app.asyncRequest('HEAD', app.globalUrl +'/v1/videos/recordplay','',header).then(res=>{

    }).catch(error=>{
      console.log(error);
    })
  },

  



  onPullDownRefresh: function () {

  },


  onReachBottom: function () {

  },


  onShareAppMessage: function () {

  }
})