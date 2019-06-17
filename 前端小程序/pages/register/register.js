const app = getApp()
const cal = require('../../utils/cal.js')

Page({
  /**
   * 页面的初始数据
   */
  data: {
    countdown: '获取验证码',
    countdownFlag: false,
    timering: false,
    form: {
      method: 'email',
      username: '',
      password: '',
      account: '',
      valid_code: ''
    },
  },

  onLoad:function(e){
    let fm = 'form.method'
    this.setData({
      [fm]:e.method
    })
  },



  countDown: function() {
    let that = this;
    let time = 30;
    let timer;

    //判断是否在倒计时
    that.setData({
      timering: true
    })

    clearInterval(timer);
    timer = setInterval(() => {
      time -= 1;
      if (time == 0) {
        console.log(123)
        that.setData({
          countdown: '重新发送',
          countdownFlag: true,
          timering: false
        })
        clearInterval(timer);
        return;
      }
      that.setData({
        countdown: '已发送 ' + time + 's',
        countdownFlag: false
      })
    }, 1000)
  },

  sendCode: function() {
    let that = this;
    console.log(that.data)

    if (!that.data.countdownFlag) {
      console.log(that.data.countdownFlag + '现在不能点击');
      return false;
    }

    let sendTime = Math.round(new Date() / 1000);
    let header = {
      "Content-Type": 'application/json'
    };
    let data = {
      method: that.data.form.method,
      account: that.data.form.account,
      time: sendTime
    }
    app.asyncRequest('POST', app.globalUrl + '/regsend', data, header).then(res => {
      that.tanKuang(res.data.body.msg);
      if (res.data.body.status != 0)return false;
      that.countDown();
    })

  },

  saveContent: function(e) {
    console.log(e.target.dataset.name);
    let key = 'form.' + e.target.dataset.name;
    let value = e.detail.value;
    this.setData({
      [key]: value,
    })

    /* 判断账号输入框 当不是倒计时的时候才可以控制发送按钮的颜色变化*/
    if (e.target.dataset.name == 'account' && !this.data.timering) {
      if (e.detail.value == '') {
        this.setData({
          countdownFlag: false
        })
      } else {
        this.setData({
          countdownFlag: true
        })
      }
    }

  },


  registerAccount: function() {
    
    let that = this;
    let header = {
      "Content-Type": 'application/json'
    };
    let data = this.data.form;
    app.asyncRequest('POST', app.globalUrl + '/reg', data, header).then(res => {
      switch (res.data.body.status) {
        case 0:
          that.tanKuang(res.data.body.msg);
          console.log(res.data.body);
          break;

        case 1:
          that.tanKuang(res.data.body.valid_code)
          break;

        case 2:
          for (var key in res.data.body.errors) {
            that.tanKuang(res.data.body.errors[key][0]);
            break;
          }
          break;
      }

    }).catch(error => {
      console.log(error)
    })
  },

  tanKuang: function(content = '内容', title = '提示') {
    wx.showModal({
      title: title,
      content: content,
      showCancel: false
    })
  },


  goC: function() {
    console.log(this.data.form)
  }


})