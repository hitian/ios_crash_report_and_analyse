## iOS crash log report and analyse

### 基于php开发的一套iOS crash log 自动解析工具.

适用于开发阶段开发同学和QA同学之间的crash日志提交和自动解析.

其实就是替代手动把log拖到xcode中解析的过程. 将开发人员从不断被打断的状态中解脱出来...

`只适用于开发阶段`, 不适用于发布的版本(拿不到log).


要发布出去的版本建议还是使用 twitter的[crashlytics](https://get.fabric.io/crashlytics) 或者 testin 来收集crash.



server端部署在任意server 上,(我是放在sae 上的, [连接](http://sae.sina.com.cn/))


client放在项目打包的Mac上.(依赖xcode 和打包时生成的dSYM), 可以由crontab 或者jenkins自动运行.


### 运行机制

QA从设备上导出的crash log.(.ips或者.crash 文件) ,使用 server 端提交原始log

client 从 server 取得未解析的log, 使用本地打包时生成的`dSYM`进行解析, 然后传回server.

开发人员直接去看server上解析好的日志就好了.


### 部署需求

server: `目前能部署在SAE上. 只是匆忙中写的一个小工具, 没有做任何安全方面的考虑, 请注意安全...`

+ php 5
+ mysql 5.1



### mysql 表结构

	SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

	--
	-- Database: `app_ioslog`
	--
	-- --------------------------------------------------------
	--
	-- Table structure for table `ios_crash_reports`
	--
	
	CREATE TABLE IF NOT EXISTS `ios_crash_reports` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `report_id` char(36) NOT NULL COMMENT 'Incident Identifier',
	  `report_key` varchar(50) NOT NULL COMMENT 'CrashReporter Key',
	  `uuid` varchar(35) DEFAULT NULL,
	  `device` varchar(30) DEFAULT NULL,
	  `origin_report` text,
	  `report` text,
	  `report_time` datetime DEFAULT NULL,
	  `ios_version` varchar(20) DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `report_id` (`report_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;



### License

MIT © HITIAN.INFO
