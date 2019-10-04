# Applaudo Challenge

# Requirements

 - Git command line
 - Composer
 - Mysql (or another DBMS supported by laravel)
 - A web server (artisan server can be used instead for development enviroments)
 - POSTMAN to test the api

# Instruction to set up the project

StackEdit stores your files in your browser, which means all your files are automatically saved locally and are accessible **offline!**

## Clone github repository

    git clone https://github.com/enripin/applaudo_challenge.git

## Install project dependencies

Move inside the applaudo_challenge folder and execute:

    composer install

With that command composer will begin download all the required dependencies

## Create .env file

Now you have to create a laravel configuration file **.env** you can just rename the **.env.example** file. Open it with a text editor and change the APP_NAME, APP_URL (if apply).

After creating the .env file run in command windows:

    php artisan key:generate

This command will update automatically the APP_KEY in the .env file. Next run:

    php artisan jwt:secret

This will create a key in the .env file that will be used by the JWT authorization.

## Configure MySQL 

Create a database where you want to store the Applaudo challenge database. Also creates an user with privileges over the previous created database.

After having created the database and user open the **.env** configuration file configure DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USER, DB_PASSWORD.

## Run migrations and seeders

    php artisan migrate --seed

If the database configuration is right all the tables and the initial records will be created automatically.

## Configuring Mail server

The applications was tested using the google SMTP configuration. Update the MAIL_DRIVER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION values.

## Deploy project

If you want to use the default artisan server you just have to execute:

    php artisan serve

If you want to use another server (example Apache, Nginx) you'll have to configute it to point to the **public** folder in the root of the project.

If used artisan server you can access to the api by http://localhost:8000

## Testing by POSTMAN

A collection has been shared in https://www.getpostman.com/collections/571986196e0d455fd7ee. Also sended by email as a collection file.

You can use the below users to test:

 - admin@domain.com password: "password"
 - client@domain.com password: "password"

In the Applaudo Challenge collection a couple of variables has been settled up to make testing easier.

 - token: The authorization token you'll get when you login in the application
 - host: Host where the application is running
 - port: Port (if using artisan serve by default is 8000)