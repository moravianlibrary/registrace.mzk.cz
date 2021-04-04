FROM registry.app.knihovny.cz/moravianlibrary/registrace/php:build

ADD web/ /var/www/

RUN chown -R www-data /var/www/
RUN chmod ugo+x /usr/local/bin/init.sh

USER www-data
WORKDIR /var/www
RUN composer update

CMD ["/usr/local/bin/init.sh"]
