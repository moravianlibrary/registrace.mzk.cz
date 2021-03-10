#!/bin/bash

# generate SSL keys fro shibboleth
mkdir -p /data/shibboleth/
openssl req \
    -x509 \
    -newkey rsa:4096 \
    -nodes \
    -keyout "/data/shibboleth/sp-key.pem" \
    -out "/data/shibboleth/sp-cert.pem" \
    -days "7300" \
    -subj "/CN=registrace.mzk.cz"

# configure shibboleth and apache
files=("/etc/shibboleth/shibboleth2.xml" "/etc/apache2/sites-enabled/000-default.conf")
for file in "${files[@]}"
do
sed -i \
   -e "s#\${PARAM_REGISTRATION_URL}#${PARAM_REGISTRATION_URL:-https:\/\/registrace.mzk.cz}#g" \
   "$file"
done
# start Shibboleth and Apache
/etc/init.d/shibd restart
apache2-foreground
