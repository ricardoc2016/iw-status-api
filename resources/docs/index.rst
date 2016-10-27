Status API
==========

Welcome to the Status API Documentation.

Index
-----

* `Requirements`_
* `Development`_
* `Installation without Docker`_
* `API Docs`_


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

Make sure you have all these tools updated to the latest version.

Clone our project and execute Composer install. Note that we use **--ignore-platform-reqs** so you can install this application even if you don't have PHP 7. Remember that we provide a Docker Compose to run this app:

```
composer install --ignore-platform-reqs;
```

Then, execute the following command:

```
docker-compose -f docker/docker-compose.yml up;
```

You will have the API ready to be used at the following base URL:

    http://localhost:37080/sta

Look at our **resources/docs/api.raml** for more information about available routes.

Documentation is available at:

    http://localhost:37080/sta/docs/index.html


Installation without Docker
---------------------------

If you want to install this application without Docker:

* Put this project in any folder you want (although we recommend you to NOT use Apache's htdocs dir).
* Copy file **install_params.sh.dist** into **install_params.sh**
* Open file **install_params.sh** and configure all the parameters.
* Execute script **install.sh**.
* Enjoy.


API Docs
--------

RAML file is available at path **resources/docs/api.raml**.

RAML website: http://raml.org



API Error Codes
---------------

========================== ============================================================================
CODE                       DESCRIPTION
========================== ============================================================================
1000                       Parameter "p" MUST be an integer >= 1
1001                       Parameter "r" MUST be an integer >= 1
1002                       Parameter "q" MUST be a string with a maximum of 120 characters.

2000                       Missing "email" parameter.
2001                       Parameter "email" MUST be a valid e-mail.
2002                       Missing parameter "status".
2003                       Parameter "string" MUST be a string with a maximum of 120 characters.

3000                       This status message does not have the confirmation code received.
3001                       Can't confirm anything with an anonymous status.

4000                       Can't delete an anonymous status.

9997                       Status message not found.
9998                       Resource not found.
9999                       Unknown Error.
========================== ============================================================================


How to run our tests?
---------------------

Inside our Docker container, go to **/development** directory and execute the following:

.. code-block:: bash

    cd /development;

    vendor/bin/phpunit;

If you want to see our code coverage:

    cd /development;

    vendor/bin/phpunit --coverage-text  --coverage-html web/coverage;

You can open the coverage html page on the following URL:

    http://localhost:37080/sta/coverage/index.html