一. 环境需求
    1. php 7.3 + , 非线程安全
    
    2. laravel5.8 
    
    3. bcmath Core ctype curl date dom fileinfo filter ftp gd gettext hash iconv json libxml， 
       mbstring mcrypt mysqli mysqlnd openssl pcntl pcre PDO pdo_mysql Phar posix Reflection session xml xmlrpc Zend OPcache zip zlib fpm gd
       
       swoolr.so 找相关人员获取
       
    4. redis 5+
    
    5. node 10 + ( 需安装 pm2)
    
    6. mysql 5.7 + 
    
    7. composer 1.9+
    
二. 搭建
    1. 拉代码
    
    2. 修改.env redis / mysql / cache / session / log 配置 （拷贝 .env.example）
    
    3. composer update
    
    4. chmod -R 777 storage
        cd storage; mkdir redis;
        cd logs; mkdir queue;
        
    5. 启动redis
        php artisan tool:redis start
    
    
