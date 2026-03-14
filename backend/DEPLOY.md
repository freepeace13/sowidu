# Deploying

Built from Laravel 8.x

## Requirements

-   PHP-8.0+
-   NPM
-   MySQL 5.7
-   Git
-   Node 14.20.0
-   Redis 3+
-   Composer
-   NginX
-   Others (library)
    -   jpegoptim
    -   pngquant

## Setup

-   Install `php` libraries.
    -   php8.x-fpm
    -   php8.x-mysql
    -   php8.x-mbstring
    -   php8.x-xml
    -   php8.x-bcmath
    -   php8.x-curl
    -   php8.x-redis
    -   php8.x-redis
    -   php8.x-gd
    -   php8.1-imagick
-   Open server ports. ie
    -   `sudo ufw allow APP_HERE`
    -   `sudo ufw allow NGINX FULL`
    -   `sudo ufw allow OPENSSH`
-   Setup `nginx` server block. e.g.

```conf
map $http_upgrade $type {
  default "web";
  websocket "ws";
}

server {
    listen 80;
    listen [::]:80;
    listen [::]:443 ssl ipv6only=on;
    listen 443 ssl;
    server_name app.example.com;
    server_tokens off;
    root /project_root_path/public;

    # SSL Managed by Certbot please do not remove
    ssl_certificate ssl_will_generate_this_fullchain.pem;
    ssl_certificate_key ssl_will_generate_this_privkey.pem;
    include ssl_will_generate_this.conf;
    ssl_dhparam ssl_will_generate_this.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files /nonexistent @$type;
    }

    location @web {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Websocket
    location @ws {
        proxy_pass             http://127.0.0.1:6001;
        proxy_set_header Host  $host;
        proxy_read_timeout     60;
        proxy_connect_timeout  60;
        proxy_redirect         off;

        # Allow the use of websockets
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;
    access_log /var/log/nginx/app.example.com-access.log;
    error_log  /var/log/nginx/app.example.com-error.log;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

-   Create `media` directory this will be used on `.env`
-   Clone the repository `git clone git@github.com:sowiduteam/app-web.git`
-   Create `packages` folder and clone repositories on `sowiduteam/packages/..`
-   Copy `env.file` using `cp .env.file .env`
-   Run `php artisan key:generate`
-   Update `.env` variables
-   Run `make deploy`
-   Make sure to run `php artisan db:seed`

## Guides / Resources

-   Open ports using [ufw](https://ubuntu.com/server/docs/security-firewall)
-   To keep queue workers running install [`supervisor`](http://supervisord.org/installing.html)
    -   Queue worker sample conf:
    ```
    [program:laravel-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /home/sowidu-dev/sites/staging.sowidu.de/artisan queue:work --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    user=sowidu-dev
    numprocs=2
    redirect_stderr=true
    stdout_logfile=/var/www/logs/app-web_worker.log
    stopwaitsecs=3600
    ```
    -   Websocket sample conf:
    ```
    [program:websockets]
    command=/usr/bin/php /home/sowidu-dev/sites/staging.sowidu.de/artisan websocket:serve
    numprocs=1
    autostart=true
    autorestart=true
    user=sowidu-dev
    stdout_logfile=/var/www/logs/websocket-app-web-worker.log
    ```
-   Create ssl `sudo certbot --nginx -d your_domain_.com`
-   Give permission:
    ```
    sudo chown -R $USER:www-data /path/to/disk
    sudo chown -R $USER:www-data /path/to/project/root
    sudo find /home/sowidu-dev/sites/sowidu.de/ -type f -exec chmod 664 {} \;
    sudo find /home/sowidu-dev/sites/sowidu.de/ -type d -exec chmod 775 {} \;
    sudo chgrp -R www-data storage bootstrap/cache
    sudo chmod -R ug+rwx storage bootstrap/cache
    sudo chmod -R a+x node_modules
    ```
-   [Setup server](https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-laravel-with-nginx-on-ubuntu-22-04)
-   [Websocket Setup](https://beyondco.de/docs/laravel-websockets/basic-usage/ssl#usage-with-a-reverse-proxy-like-nginx)
