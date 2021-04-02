#!/bin/bash

# read shibboleth private key from secrets
if [ ! -f "/etc/shibboleth/sp-key.pem" ] && [ -f "/etc/secrets/sp-key.pem" ]; then
  ln -s  "/etc/secrets/sp-key.pem" "/etc/shibboleth/sp-key.pem"
fi

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
