#!/bin/bash
set -x
set -e
docker build -t moravianlibrary/registrace-php:build -f build/php-image/Dockerfile build/php-image
docker build -t moravianlibrary/registrace/app:build -f build/app/Dockerfile .
docker-compose up
