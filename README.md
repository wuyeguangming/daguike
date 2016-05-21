#大贵客
##安装步骤：
* npm install
* bower install
* composer install
* create database and modify the config
* php artisan migrate
* php artisan db:seed
* 为 app/storage 目录下的文件设置写权限
* (由于主机宝固的原因，需将 bootstrap\paths.php 中的public路径改为public_html)
* 修改Nginx配置
```
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
```
* 对于php：
    * 开启php_fileinfo
* 对于apache：
    * 开启mod_rewrite
    * 修改DocumentRoot "d:/www/public" -> DocumentRoot "d:/www/public_html"
    * 修改Directory "d:/www/public" -> Directory "d:/www/public_html"
