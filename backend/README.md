# Sowidu

Built from Laravel 5.6

## Prerequisites

Ensure that you have these softwares running in your system

-   PHP-8.0+
-   NPM
-   MySQL 5.7
-   Git
-   Node 14.20.0
-   Redis 3+
-   Composer

## Installation

-   Clone the repository into your working directory by running
-   Create database and update `.env`
-   Run `make setup`

## Environment

To prepare working environment files, clear cache and set permissions, run `make setup`. The `.env` file is generated you may now substitute the database credentials base on your setting, then run:

```
$ php artisan migrate --seed
```

## Development

-   Must disable `git fileMode` on detecting file permission changes. Run `git config core.fileMode false`

## Internal Packages

This application includes internal packages located in the `./packages/` directory. These packages use the `Packages\{PackageName}\` namespace pattern.

### Available Packages

- **active-status** - Database-driven active status management for Eloquent models
- **addressable** - Address management functionality for models
- **avatarable** - Avatar/file management for models
- **contacts** - Contact relationship management (contactships)
- **translation** - Database-driven translation loader and manager

### Package Configuration

Packages are registered in `composer.json` as path repositories:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/*",
            "options": {
                "symlink": false
            }
        }
    ]
}
```

Each package includes comprehensive documentation in its `README.md` file. See individual package directories for usage instructions.

### Package Namespaces

All internal packages use the `Packages\{PackageName}\` namespace:

- `Packages\ActiveStatus\`
- `Packages\Addressable\`
- `Packages\Avatarable\`
- `Packages\Contacts\`
- `Packages\Translation\`

## Vuex Dev Tool

One of the useful tool for debugging state in vuex is to install chrome [vue.js dev tool](https://chrome.google.com/webstore/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd?hl=en).

## Server

If you prefer to use NGINX or APACHE to serve the application you need install it and configure virtual host pointing to `sowidu-version2/public` folder, otherwise laravel have built-in development server ready to use.

```
$ php artisan serve
```

## Compiling Assets

To compile assets we use `laravel-mix` to handle everything we need. To use it run:

```

```

In `production` we minify our scripts and styling

```
$ npm run production
```

## Socket.io

The application is broadcasting events through websockets to implement real time, live-updating user interface.
We use `Redis` as broadcaster together with `Socket.io` and in order work this together you should have `Socket.io server` in your system.
I assume you are working in linux environment, if not, please find good documentations online that match the requirements.

Procedures:

-   Follow the instruction here how to configure and install [redis (Ubuntu 18)](https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-redis-on-ubuntu-18-04) or [redis (Ubuntu 16)](https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-redis-on-ubuntu-16-04)
-   Install [laravel-echo-server](https://github.com/tlaverdure/laravel-echo-server) globally by running `npm install -g laravel-echo-server` in your terminal
-   To run the `laravel-echo-server`, navigate to the root folder of the application and run `laravel-echo-server start` and you should see something like this

```

```

## Phpmyadmin

Credentials for live server database

```
Username: sowidu_user
Password: kYxU6xicYjXJfM
Database: sowidudb_v2
```

## Super User Account

Credentials for Super User Seeder

```
Username: sg
Email: sg@sowidu.de


### Running with Docker

-   Install docker engine `sudo apt-get update` & `sudo apt-get install docker-ce docker-ce-cli containerd.io`
-   Install docker-compose, go to root folder `sudo curl -L "https://github.com/docker/compose/releases/download/1.26.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose` and assign permission `sudo chmod +x /usr/local/bin/docker-compose`

-   Set `.env` for Redis

```

REDIS_PORT=6380
DB_PORT=3307

```

### Running docker

```

docker-compose build

## Run the container in background add `-d` option

docker-compose up

```

From fresh installed app:

```

docker run --rm \
-u "$(id -u):$(id -g)" \

## Requirements

-   php8.0-gd
-   php8.0-imagick
-   [Ghostscript](https://ghostscript.com/releases/gsdnld.html)

## Installing Ghostscript (Linux)

-   Download source code and extract
-   Navigate to root folder of the extracted files
-   Run `./configure && make && make install`

## Contributing

Git branches:

-   `main` = production
-   `staging` = staging

IMPORTANT!

1. We don't have "development" branch!
2. New branch must be created from `main` each pull request.
3. Create PR only when merging to `main`, otherwise do it manually.
4. Merge your branch to `staging` for testing.

Sample for deploying `modules/invoicify` branch to `staging`.

```bash
    $ git(modules/invoicify): git checkout staging
    $ git(staging): git merge modules/invoicify
    // Resolve any conflicts then commit and push the updated files
    $ git(staging): git add .
    $ git(staging): git commit
    $ git(staging): git push origin staging
```
