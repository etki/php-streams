# Please build from project root using
# `-f resources/docker/php-7-test-runner/Dockerfile` key

FROM php:7
MAINTAINER Etki <etki@etki.name>

RUN apt-get update -yq && apt-get install git zlib1g-dev -yq

RUN docker-php-ext-install zip mbstring

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ADD . /var/streams

WORKDIR /var/streams

RUN composer install

ENTRYPOINT ["/var/streams/bin/codecept"]

CMD ["run"]
