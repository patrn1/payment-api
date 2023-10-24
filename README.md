
# Installation
```
USER_ID=$(stat -c %u .)
GROUP_ID=$(stat -c %g .)

cp -n .env.test .env
chown $USER_ID:$GROUP_ID .env

echo GROUP_ID=$GROUP_ID >> .env


docker-compose up -d

docker restart payment_service_nginx payment_service_php

docker exec -it payment_service_php composer install
```

## Run migrations
```
docker exec -it payment_service_php bin/console doctrine:migrations:migrate
```
## Run fixtures
```
docker exec -it payment_service_php php bin/console doctrine:fixtures:load --append
```
## CURL test request examples
```
curl 'http://localhost/purchase' --data-raw '{"taxNumber":"DE120948261","product":2,"paymentProcessor":"paypal"}'

curl 'http://localhost/calculate-price' --data-raw '{"taxNumber":"DE120948261","product":2,"paymentProcessor":"paypal"}'
```
## RUN PHPUnit tests
```
docker exec -it payment_service_php bin/phpunit
```

