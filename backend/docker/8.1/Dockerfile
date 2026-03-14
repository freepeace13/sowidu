FROM ubuntu:22.04

LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG POSTGRES_VERSION=13

ENV NVM_DIR=/usr/local/nvm
ARG NODE_VERSION=14.20.0

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y wget gnupg gosu curl ca-certificates cmake zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 \
    && mkdir -p ~/.gnupg \
    && chmod 600 ~/.gnupg \
    && echo "disable-ipv6" >> ~/.gnupg/dirmngr.conf \
    && echo "keyserver hkp://keyserver.ubuntu.com:80" >> ~/.gnupg/dirmngr.conf \
    && gpg --recv-key 0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c \
    && gpg --export 0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c > /usr/share/keyrings/ppa_ondrej_php.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y \
    imagemagick imagemagick-doc ffmpeg optipng jpegoptim \
    php8.1-exif php8.1-cli php8.1-dev \
    php8.1-pgsql php8.1-sqlite3 \
    php8.1-curl php8.1-memcached \
    php8.1-imap php8.1-mysql php8.1-mbstring \
    php8.1-xml php8.1-zip php8.1-bcmath php8.1-soap \
    php8.1-intl php8.1-readline php8.1-pcov \
    php8.1-msgpack php8.1-igbinary php8.1-ldap \
    php8.1-redis php8.1-swoole php8.1-xdebug \
    php8.1-imagick php8.1-gd \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    # && curl -sLS https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    # && apt-get install -y nodejs \
    # && npm install -g npm \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /usr/share/keyrings/yarnkey.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/yarnkey.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /usr/share/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y yarn \
    && apt-get install -y mysql-client \
    && apt-get install -y postgresql-client-$POSTGRES_VERSION \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install GhostScript
RUN mkdir -p /installs
RUN cd /installs && wget https://github.com/ArtifexSoftware/ghostpdl-downloads/releases/download/gs10012/ghostscript-10.01.2.tar.gz
RUN cd /installs && tar -xvf ghostscript-10.01.2.tar.gz
RUN cd /installs/ghostscript-10.01.2 && ./configure && make && make install

# Install libde265
RUN cd /installs && wget https://github.com/strukturag/libde265/releases/download/v1.0.12/libde265-1.0.12.tar.gz
RUN cd /installs && tar -xvf libde265-1.0.12.tar.gz
RUN cd /installs/libde265-1.0.12 && ./autogen.sh && ./configure && make -j$(nproc) && make install

# Install libheif
RUN cd /installs && wget https://github.com/strukturag/libheif/releases/download/v1.16.2/libheif-1.16.2.tar.gz
RUN cd /installs && tar -xvf libheif-1.16.2.tar.gz
RUN cd /installs/libheif-1.16.2 && mkdir build && cd ./build && cmake --preset=release .. && make -j$(nproc) && make install

# Override Imagick policy.xml
RUN rm /etc/ImageMagick-6/policy.xml
COPY imagick/policy.xml /etc/ImageMagick-6/policy.xml

# Install nvm
RUN mkdir $NVM_DIR
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash

# replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

# install node and npm
RUN source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# add node and npm to path so the commands are available
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

# confirm installation
RUN node -v
RUN npm -v

RUN update-alternatives --set php /usr/bin/php8.1

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.1

RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.1/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
