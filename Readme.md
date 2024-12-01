## 功能简介：
本项目是对 [youshandefeiyang/allinone](https://hub.docker.com/r/youshandefeiyang/allinone) /tv.m3u 和 /tptv.m3u 进行聚合 & 重新分组。

## 前置项目
请先安装此项目 [youshandefeiyang/allinone](https://hub.docker.com/r/youshandefeiyang/allinone)

## 运行方式：
```shell

##########################
#                        #
#    Docker 运行方式      #
#                        #
##########################

# docker 运行方式

# 将 /path/to/config/ 改为你的配置文件存放目录
docker run -d --restart=unless-stopped -v /path/to/config/:/app/config/ -p 35456:35456 --name allinone_format yuexuangu/allinone_format:latest

# openwrt 等系统可能需要 --net=host 网络的，请使用以下命令：
docker run -d --restart=unless-stopped --net=host -v /path/to/config/:/app/config/ -p 35456:35456 --name allinone_format yuexuangu/allinone_format:latest

##########################
#                        #
#      源码运行方式       #  
#                        #
##########################

# 前端
cd frontend && npm install && npm run dev
# 后端
cd server && php -S 0.0.0.0:35456 index.php

```

## 使用教程
- 部署后访问配置页面：http://内网IP:35456/
- 配置 allinone tv.m3u url 请求地址
- 访问配置页面显示的频道链接（三种 m3u 格式, 三种 txt 格式），测试是否正常
- 在播放器配置 m3u/txt 频道链接

## 配置管理
![配置管理](./images/config.jpeg)

## 更新日志
```text
2024-12-02 00:09:57
    - 重构项目
    - 新增 配置管理页面
    - 优化 三种 m3u 输出格式 & 三种 txt 输出格式
旧日志：
    请查看 deprecated/Readme.md
```
