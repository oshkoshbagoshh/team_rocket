# WordPress + msmtp so PHP mail() is relayed to MailHog for local testing.
FROM wordpress:php8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends msmtp \
    && rm -rf /var/lib/apt/lists/*

# Route all outbound mail to the MailHog SMTP listener on the compose network.
RUN printf 'sendmail_path = "/usr/bin/msmtp -t --host=mailhog --port=1025 --from=wordpress@kanjava.test"\n' \
    > /usr/local/etc/php/conf.d/zz-mailhog.ini
