#!/bin/bash
set -x
set -e
docker build -t moravianlibrary/registrace/php:build -f build/php-image/Dockerfile build/php-image
docker image tag moravianlibrary/registrace/php:build registry.app.knihovny.cz/moravianlibrary/registrace/php:build
docker build -t moravianlibrary/registrace/app:build -f build/app/Dockerfile .
docker image tag moravianlibrary/registrace/app:build registry.app.knihovny.cz/moravianlibrary/registrace/app:build
docker-compose up
