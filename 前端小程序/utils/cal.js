/* 小数计算 */
let obj = {
  //加法
  cPlus: function (x, y) {
    let m, r1, r2;
    try { r1 = x.toString().split('.')[1].length } catch (e) { r1 = 0 }
    try { r2 = y.toString().split('.')[1].length } catch (e) { r2 = 0 }
    m = Math.pow(10, Math.max(r1, r2))
    // console.log(x * m, y * m)
    return (x * m + y * m) / m
  },
  // 减法
  cSub: function (x, y) {
    let m, n, r1, r2;
    try { r1 = x.toString().split('.')[1].length } catch (e) { r1 = 0 }
    try { r2 = y.toString().split('.')[1].length } catch (e) { r2 = 0 }
    m = Math.pow(10, Math.max(r1, r2))
    n = (r1 >= r2) ? r1 : r2
    return Number(((x * m - y * m) / m).toFixed(n))
  },

  //乘法
  cMul: function (x, y) {
    let m = 0, r1, r2
    try { m += x.toString().split('.')[1].length } catch (e) { }
    try { m += y.toString().split('.')[1].length } catch (e) { }
    r1 = Number(x.toString().replace('.', ''))
    r2 = Number(y.toString().replace('.', ''))
    return r1 * r2 / Math.pow(10, m)
  },

  //除法
  cDiv: function (x, y) {
    let m1 = 0, m2 = 0, r1, r2
    try { m1 = x.toString().split('.')[1].length } catch (e) { }
    try { m2 = y.toString().split('.')[1].length } catch (e) { }
    r1 = Number(x.toString().replace('.', ''))
    r2 = Number(y.toString().replace('.', ''))
    return this.cMul(r1 / r2, Math.pow(10, m2 - m1))
  },
}

module.exports = {
  calculate: obj
}