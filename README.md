# API project with Slim framework and Docker!

This project implements a very simple Rest API using the PHP Slim framework and Docker.

Once you have cloned this project, run the following command in the project directory :

    docker-compose up

Then you will have to install all the dependencies with composer via:

    docker exec php-fpm composer install 

Finally, we migrate the data with the following command:

    docker exec php-fpm console/doctrine orm:schema-tool:create