FROM mariadb:latest

ARG GROUP_ID

RUN groupadd -g ${GROUP_ID} www

RUN usermod -a -G www mysql
