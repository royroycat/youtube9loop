## Introduction
It is a very old project. The running site is [http://www.youtube9loop.com](http://www.youtube9loop.com). It is using Yii 1. And due to the history, the Yii source code is also included in the repo.

## Install
 

## nignx setting

```
server {
    listen 80;
    listen 443 ssl;
    server_name  youtube9loop.com *.youtube9loop.com;
    return       301 $scheme://www.youtube9loop.com$request_uri;
}

server {
    listen 80;
    listen 443 ssl;

    set $host_path "/youtube9loop";

    server_name  www.youtube9loop.com;
    root   /usr/share/nginx/$host_path;
    set $yii_bootstrap "index.php";

    charset utf-8;

    location / {
        index  index.html $yii_bootstrap;
        try_files $uri $uri/ /$yii_bootstrap?$args;
    }

    location ~ ^/(protected|framework|themes/\w+/views) {
        deny  all;
    }

    #avoid processing of calls to unexisting static files by yii
    location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
        try_files $uri =404;
    }

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    location ~ \.php {
        fastcgi_split_path_info  ^(.+\.php)(.*)$;

        #let yii catch the calls to unexising PHP files
        set $fsn /$yii_bootstrap;
        if (-f $document_root$fastcgi_script_name){
            set $fsn $fastcgi_script_name;
        }

        #fastcgi_pass   127.0.0.1:9000;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        include fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fsn;

        #PATH_INFO and PATH_TRANSLATED can be omitted, but RFC 3875 specifies them for CGI
        fastcgi_param  PATH_INFO        $fastcgi_path_info;
        fastcgi_param  PATH_TRANSLATED  $document_root$fsn;
    }

    location ~ /\.well-known {
	allow all;
    }

    # prevent nginx from serving dotfiles (.htaccess, .svn, .git, etc.)
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
}
```

