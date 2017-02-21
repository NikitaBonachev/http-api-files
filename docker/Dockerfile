FROM debian:jessie

# PHP
RUN rm -rf /var/lib/apt/lists* \
&& apt-get update \
&& apt-get install -y php5 php5-mysql php5-curl php5-gd php5-mcrypt php5-imagick

# Apache
RUN a2enmod rewrite \
&& rm -rf "/var/lock/apache2"
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY php.ini /etc/php5/apache2/conf.d/99-php.ini
EXPOSE 80 443

# Composer
COPY composer.phar /usr/bin/composer
RUN chmod 0744 /usr/bin/composer

COPY run.sh /run.sh
RUN chmod +x /run.sh
CMD /run.sh
