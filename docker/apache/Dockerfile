FROM php:8.3-apache

# パッケージのインストールと PHP拡張のセットアップ
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
    vim \
    iputils-ping \
    net-tools \
    sudo \
    gnupg \
    libpng-dev \
    libjpeg62-turbo-dev \
    curl \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && a2enmod rewrite

# Composerの配置（composerイメージからコピー）
COPY --from=composer /usr/bin/composer /usr/bin/composer

# ApacheとPHPの設定ファイルを追加
ADD docker/apache/php.ini /usr/local/etc/php/
ADD docker/apache/config/000-default.conf /etc/apache2/sites-available/
RUN a2ensite 000-default

# Node.jsとnpmのインストール（NodeSource公式）
ENV NODE_VERSION=22.14.0
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version

# 作業ディレクトリ設定とアプリコード配置
WORKDIR /var/www/vhosts/webapp/
COPY ./webapp /var/www/vhosts/webapp

# 権限設定とComposer install
RUN chown www-data storage/ -R \
    && COMPOSER_ALLOW_SUPERUSER=1 composer install
