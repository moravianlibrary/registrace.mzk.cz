#!/bin/bash
docker build -t moravianlibrary/registration .
docker image tag moravianlibrary/registration:latest registry.app.knihovny.cz/moravianlibrary/registration:latest
docker image push registry.app.knihovny.cz/moravianlibrary/registration:latest
