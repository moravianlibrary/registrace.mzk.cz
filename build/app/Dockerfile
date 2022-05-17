FROM localhost/moravianlibrary/registrace/php:build
ADD web/ /var/www/

RUN chown -R www-data /var/www/


USER www-data
WORKDIR /var/www
RUN composer update

USER root
ENTRYPOINT [ "/container-entrypoint.sh" ]
CMD ["apache"]
