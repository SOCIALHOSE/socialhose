# Setup

### Requirement

- php

  Version: greater or equal to 7.2

  Extensions:

  - bcmath
  - libxml
  - mbstring
  - openssl
  - xml
  - curl

- ElasticSearch 5.x
- RabbitMQ
- Apache2

### Install

1.  Run

        composer install
        ./bin/console doctrine:database:create
        ./bin/console doctrine:migrations:migrate --no-interaction
        ./bin/console socialhose:fixture:load --force
        setfacl -dR -m u:"www-data":rwX -m u:$(whoami):rwX var
        setfacl -R -m u:"www-data":rwX -m u:$(whoami):rwX var

2.  Use configuration from `configuration` directory

3.  Regenerate public and private keys in **app/config/cert** and update options in **app/config/parameters.yml**
    - jwt_private_key_path
    - jwt_public_key_path
    - jwt_key_pass_phrase

# Tests

    composer test

or separately

    composer unit-test
    composer behat-test

Also you can run test by hand, like this:

- Run unit test

        phpunit

- Run all functional test

        behat -s api
        behat -s command

  or for group

        behat -s api behat/features/Security

  or for concrete feature

        behat -s api behat/features/Security/token/create.feature

  or run in debug mode (behat will print out all request options and responses)

        DEBUG=true behat -s api

  or for fast test, without recreating database and cache clear

        WITHOUT_CLEAR=true behat -s api

### Docker

- Add UID to environment file: `echo "UID=$UID" >> .env`
- docker-compose up --build -d
  - make sure all containers build and run successfully (`docker-compose ps`):
    - socialhose-elastic
    - socialhose-elastic-hq
    - socialhose-mysql
    - socialhose-php
    - socialhose-rabbit
- `docker-compose exec socialhose-php bash`
  Go into container. All next commands should be running in it.
- install \ update backend
  - `sudo setfacl -dR -m u:"www-data":rwX -m u:$(whoami):rwX var`
  - `sudo setfacl -R -m u:"www-data":rwX -m u:$(whoami):rwX var`
  - `cp app/config/parameters.yml.docker app/config/parameters.yml`
  - `composer1 install`
- database migration
  - `./bin/console doctrine:migrations:migrate --no-interaction`
  Migration is broken and can temporarily be bypassed by commenting out the up commands in VersionVersion20210212114326
  - `./bin/console socialhose:fixture:load --force`
- install \ update frontend
  - `cp frontend/app/appConfig.js.docker frontend/app/appConfig.js`
  - `cd frontend`
  - `npm install`
  - `npm run build`

## Add twitter, instagram fixtures

- `docker-compose exec socialhose-php bash`
- `cd socialhosefixtures`
- `./add-fixtures.sh`

## Services are available under following urls:

- http://localhost:8081/app_dev.php - main site
- http://localhost:5000 - elastic hq (use http://socialhose-elastic:9200 for connect to elastic service)
- http://localhost:15672 - rabbitMQ UI
- MySQL external port is 33066
- http://localhost:8025/ - mail UI
