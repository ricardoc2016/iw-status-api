Status API
==========

Welcome to the Status API Documentation.

Index
-----

* Requirements
* Development
* Installation without Docker
* API Docs


Requirements
------------

* PHP 7.0+
* Apache 2.2+


Development
-----------

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


Installation without Docker
---------------------------

If you want to install this application without Docker:

* Put this project in any folder you want (although we recommend you to NOT use Apache's htdocs dir).
* Open file **install.sh** and set up


API Docs
--------

RAML file is available at path **docs/api.raml**.

RAML website: http://raml.org

**Error Codes**

========================== ============================================================================
CODE                       DESCRIPTION
========================== ============================================================================
1000                       Parameter "p" MUST be an integer >= 1
1001                       Parameter "r" MUST be an integer >= 1
1002                       Parameter "q" MUST be a string with a maximum of 120 characters.

9999                       Unknown Error.
========================== ============================================================================