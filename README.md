## 微信小程序 外卖点餐-视频点赞

### ---模拟商品购买---  
![Image Text](https://github.com/promisegg/wx_miniapp/blob/master/images/one.gif)  

### 商品分类左右联动  
#### 渲染时记录每个分类距离顶部的高度，滚动的时候跟scrollTop进行比较  
      const query = wx.createSelectorQuery();
      let tops = [];
      let topViewH, navViewH, scrollHeight, fixedCateH;
      query.selectAll('.top,.nav,.mid').boundingClientRect(function(heights) {
        topViewH = heights[0].height //顶部描述的高度
        navViewH = heights[1].height //三个小导航的高度
        scrollHeight = heights[2].height
      }).exec()

      query.selectAll('.good-title').boundingClientRect(function(rects) {
        rects.forEach(function(rect, index) {
          //减去顶部固定占位置view的高度
          tops[index] = Math.round(rect.top) - topViewH - navViewH
        })
        that.setData({
          tops: tops //把每个分类距离scroll-view顶部的距离数组记录下来
        })
      }).exec()

### ---视频部分---  
![Image Text](https://github.com/promisegg/wx_miniapp/blob/master/images/two.gif)  


### ---后台管理截图---  
#### 商品管理  
![Image Text](https://github.com/promisegg/wx_miniapp/blob/master/images/three.jpg)  
#### 视频管理  
![Image Text](https://github.com/promisegg/wx_miniapp/blob/master/images/four.jpg)  