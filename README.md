# Laravel 日志驱动 
Laravel 自带的日志驱动，只能支持按照日期才分日志。线上翻了一遍，也没发现有更加好的驱动。所以自己实现了一个

### 日志驱动效果
1. 支持按照类+日期分做日志分割 
2. 日志详情增加了【file】 显示日志埋点详细位置 【pid】进程号，【traceId】 全局唯一索引，【path】 路由信息

#### 日志驱动效果 图：

##### 埋点：
![image](https://user-images.githubusercontent.com/21374954/144803541-f7cb2896-b0b8-4a4d-99d6-d72f03a52c28.png)

##### 日志展示：
![image](https://user-images.githubusercontent.com/21374954/144803571-e34e973b-bc12-4de0-99a3-7ccfb45d9fc5.png)

##### 日志内容：
![image](https://user-images.githubusercontent.com/21374954/144803685-86caf272-80fb-42a8-8fd7-09512d0a1df0.png)


### 按照教程
1.安装这个包的时候你的 composer.json 在require可以加这样一行：   "yueguang/yueguang-log-drive": "dev-main"
2.下面安装这个自定义包吧： composer update yueguang/yueguang-log-drive
3.执行 composer dump-autoload

大功告成！

注意：目前日志驱动只支持Laravel 框架

开源不容易，各位大佬如果觉得好用，点个赞或者打赏一下，支持一下，谢谢大家


![a0c1a444651d292cb6862c58de04bdc](https://user-images.githubusercontent.com/21374954/144804788-2b0024a3-d383-4581-af30-cf944f410274.jpg)




