FROM php:7.4-cli

ARG userid=1000
ARG groupid=1000

WORKDIR /newdir

RUN groupadd -g ${groupid} newgroup && \
	useradd -g ${groupid} -u ${userid} newuser -m && \
	chown newuser:newgroup /newdir

COPY --from=composer:2.4.1 /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get -y install libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev unzip vim

RUN docker-php-ext-install pdo_pgsql && \
	docker-php-ext-install pdo_mysql && \
	docker-php-ext-install exif && \
	docker-php-ext-configure gd --with-jpeg && \
	docker-php-ext-install gd && \
	docker-php-ext-install zip

USER newuser
RUN	composer global require laravel/installer
ENV PATH "$PATH:/home/newuser/.composer/vendor/bin"

EXPOSE 5000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=5000"]
