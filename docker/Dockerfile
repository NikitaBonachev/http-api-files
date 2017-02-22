FROM debian:jessie

# PHP
RUN rm -rf /var/lib/apt/lists* \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get -y install php5 php5-mysql php5-curl php5-gd php5-mcrypt php5-imagick mysql-server python-setuptools mysql-client php5-mysql

# mysql config
ADD my.cnf /etc/mysql/conf.d/my.cnf
RUN chmod 664 /etc/mysql/conf.d/my.cnf

# apache config
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
RUN chown -R www-data:www-data /var/www/
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY php.ini /etc/php5/apache2/conf.d/99-php.ini
EXPOSE 80 443

# Composer
COPY composer.phar /usr/bin/composer
RUN chmod 0744 /usr/bin/composer

# Supervisor Config
RUN mkdir /var/log/supervisor/
RUN /usr/bin/easy_install supervisor
RUN /usr/bin/easy_install supervisor-stdout
ADD ./supervisord.conf /etc/supervisord.conf

COPY run.sh /run.sh
RUN chmod +x /run.sh
CMD ["/run.sh"]
