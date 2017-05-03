# yishu
  用户日常衣物记录与穿着习惯推测系统旨在记录、统计用户每日的衣着数据并提供以衣着为中心的社交（类朋友圈）。项目主要为Linux服务器+Ci框架+Mysql实现Web Api，并没有Web界面。 主要分服务器端、IOS端、嵌入IOS应用的WEB APP。
   个人负责的是服务器端，代码也是服务器的代码。 
   
[项目部署服务器地址](https://myishu.top/yishu "https://myishu.top/yishu")  

IOS应用端未上线无法展示，只能展示关于社交的WEB APP的DEMO。

[WEB APP的DEMO地址](https://myishu.top/dist "https://myishu.top/dist")  

[控制器](./application/controllers)----------业务逻辑处理  

[模型](./application/models)-------------数据处理  

[封装模型方法](./application/core/MY_Model.php)--------token解析，控制层到模型的数据处理，调试打印数据、打印日志，多维数组排序，图片路径组装等  

[文档](document)--------需求文档、接口文档
