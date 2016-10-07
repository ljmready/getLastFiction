<?php 
$config = [
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PWD' => 'root',
    'DB_NAME' => 'story',

    //试验成功的有qq邮箱、阿里企业邮
    'MAIL_HOST' => 'smtp.mxhichina.com;', //邮箱host，可以填多个，用;分开
    'MAIL_USERNAME' => '', //邮箱用户名
    'MAIL_PASSWORD' => '', //邮箱密码,如果是qq邮箱则填授权码
    'MAIL_PORT' => '25', //邮箱端口，如果是qq邮箱则填465
    'MAIL_FROM' => '', //发件人
    'MAIL_TO' => '', //收件人
    'MAIL_SECURE' => 'ssl', //收件人，qq邮箱填ssl，阿里云填tls
];
//要拉取的小说链接
//目前只支持笔趣阁网
$story = [
    [
        'STORY_NAME' => '完美世界',
        'FIRST_SECTION_URL' => 'http://www.biquge.la/book/14/9609.html',
        'START_SECTION_URL' => '',
    ],
    [
        'STORY_NAME' => '雪鹰领主',
        'FIRST_SECTION_URL' => 'http://www.biquge.la/book/5094/3118156.html',
        'START_SECTION_URL' => '',
    
    ]

];
