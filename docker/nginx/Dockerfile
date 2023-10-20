FROM nginx:latest

ARG GROUP_ID

RUN groupadd -g ${GROUP_ID} www

RUN usermod -a -G www nginx

# CMD chmod -R 777 /var/www

# CMD ["nginx", "-g", "daemon off;"]