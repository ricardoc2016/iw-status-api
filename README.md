# IW Status API

Status API project.

## Development

Install Docker + Docker Compose.

* Docker Installation Instructions: https://docs.docker.com/engine/installation
* Docker Compose Installation Instructions: https://docs.docker.com/compose/install

Make sure to have both of them updated to the latest version.

Clone our project and then, execute the following command:

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