#!/bin/bash
set -x
set -e
docker build -t moravianlibrary/registrace-php:build -f build/php-image/Dockerfile build/php-image
docker image tag moravianlibrary/registrace-php:build registry.app.knihovny.cz/moravianlibrary/registrace-php:build
docker image tag registry.app.knihovny.cz/moravianlibrary/registrace-php:build docker.app.knihovny.cz/moravianlibrary/registrace-php:build
docker image push registry.app.knihovny.cz/moravianlibrary/registrace-php:build
docker build -t moravianlibrary/registrace:build -f build/app/Dockerfile .
docker image tag moravianlibrary/registrace:build registry.app.knihovny.cz/moravianlibrary/registrace:build
docker image push registry.app.knihovny.cz/moravianlibrary/registrace:build
