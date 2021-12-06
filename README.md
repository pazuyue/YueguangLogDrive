# Laravel 日志驱动 
Laravel 自带的日志驱动，只能支持按照日期才分日志。线上翻了一遍，也没发现有更加好的驱动。所以自己实现了一个

### 日志驱动效果
1. 支持按照类+日期分做日志分割 
2. 日志详情增加了【file】 显示日志埋点详细位置 【pid】进程号，【traceId】 全局唯一索引，【path】 路由信息

#### 日志驱动效果 图：

![image](https://user-images.githubusercontent.com/21374954/144803541-f7cb2896-b0b8-4a4d-99d6-d72f03a52c28.png)
