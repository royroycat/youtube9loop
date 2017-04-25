## Introduction
It is a very old project. The running site is [http://www.youtube9loop.com](http://www.youtube9loop.com). It is using Yii 1. And due to the history, the Yii source code is also included in the repo.

## Install
 1. [mysql] Run `youtube9loop.sql` for the DB schema
 2. [php] check out this project and put it in your fav place. In this project, I put in `/usr/share/nginx/$host_path`
 3. [nginx] setting up the nginx config, a sample is at below. It is using host `www.youtube9loop.com` as example
 4. [yii config] go to `/protected/config/main.php` fill back `***my-sql-username***`, `***my-sql-password***`, `***youtube-api-key***`
 5. for `youtube-api-key`, you can obtain it by Google
 6. for `***only-your-ip***`, gii is GUI of yii, but the admin GUI you may want to access by your machine only, so setting your local machine IP may better
 7. [js hardcode] js/customPlayer.js, there also one `youtube-api-key` you need to fill in
 8. [optional] go to `protected/config/params.php`, `***your-email-address***@gmail.com` is your email address obviously. `default_youtube_id` is the song will be play when user have not put any youtubeid be GET parameter
 9. you will need bcmath package, for 7.0: sudo apt-get install php7.0-bcmath
 10. Access your youtube9loop site now!  
 (11. Although we use composer. but the vendor package are already downloaded for you. You may update composer.json for your own risk)

## If you are using php7
When running the site, it will show an error in ExtendedController line 3
Please modified the ExtendedController in /protected/components as: where the $action is missing

```
<?php

class ExtendedController extends CController
{
	
	public function beforeAction($action)
	{
		return true;
	}
	
	public function afterAction($action)
	{
		return true;
	}
}
```


## nginx setting

```
server {
	# redirect all non-www access to www.youtube9loop.com
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

