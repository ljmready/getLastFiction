使用方法
- 导入story.sql
- 修改app.php中的相关配置
- 命令行下抓取最新章节到数据库
```

php app.php

```

- 定时任务 发送email
小心邮箱爆了。

```
crontab -e

#加入
* * * * * php /dir/sendmail.php
```

修改了phpmailer/class.phpmailer.php的字符集参数
```
public $CharSet = 'iso-8859-1';
改成
public $CharSet = 'utf-8';
```
