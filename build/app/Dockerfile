FROM docker.app.knihovny.cz/moravianlibrary/registrace-php:build

ADD web/ /var/www/

WORKDIR /var/www
RUN composer update
RUN chmod ugo+x /usr/local/bin/init.sh

CMD ["/usr/local/bin/init.sh"]
