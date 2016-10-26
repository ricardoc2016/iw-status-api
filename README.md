# IW Status API

Status API project.

## Requirements

* PHP 7.0+
* Apache 2.2+

## Development

Install Composer + Docker + Docker Compose.

* Composer Installation Instructions: https://getcomposer.org/download
* Docker Installation Instructions: https://docs.docker.com/engine/installation
* Docker Compose Installation Instructions: https://docs.docker.com/compose/install

Make sure to have both of them updated to the latest version.

Clone our project and execute Composer install (we use --ignore-platform-reqs so you can install even if you don't have PHP 7. Remember that we provide a Docker Compose to run this app)':

```
composer install --ignore-platform-reqs;
```

Then, execute the following command:

```
docker-compose -f docker/docker-compose.yml up;
```

You will have the API ready to be used at the following URL:

    http://localhost:37080/sta

## Installation without Docker

If you want to install this application without Docker:

* Put this project in any folder you want (although we recommend you to NOT use Apache's htdocs dir).
* Open file **install.sh** and set up

## API Docs

RAML file is available at path **resources/docs/api.raml**.

RAML website: http://raml.org