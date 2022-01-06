# Project Title

ALESHA TECH - ALESHA RIDE

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

## Installation

Download or clone the code from repository.
Unzip the zip file or Run this command to clone

    git clone https://gitlab.com/aleshatech/web-backend.git


Open browser; goto [localhost/phpmyadmin](http://localhost/phpmyadmin).

Create a database with name **alesha_ride** and import the file **alesha_ride.sql** in that database.

Copy the remaining code into your root directory:

for example, for windows

**WAMP : /wamp/www/web-backend**

OR

**XAMPP : /xampp/htdocs/web-backend**

OR

**DOCKER :**

### Prerequisites

What things you need to install the software and how to install them


## Requirements

      - PHP version: 7.3 or newer
      - MySQL database (or access to create one) version: 5.7 or newer
      - MySQLi module for PHP
      - GD module for PHP


## How to install        
 
Go to web-backend folder
    
    cd web-backend
    
Pull the docker image
    
    docker pull tomsik68/xampp
    
Run the container
    
    docker run --name alesha-ride -p 5000:22 -p 5001:80 -d -v /path/to/your/app/web-backend:/www tomsik68/xampp

MySQL Details

      - MySQL Username = root
    
      - MySQL Password = 
    
      - MySQL Database = alesha_ride

then

import database

      localhost:5001/phpmyadmin    
    
    
## For Upload Folder permission

      - sudo chgrp -R www-data storage  /path/to/your/app/public/images/car_images
    
      - sudo chmod -R ug+rwx  storage  /path/to/your/app/public/images/companies
    
      - sudo chmod -R ug+rwx  storage  /path/to/your/app/public/images/gofer
    
      - sudo chmod -R ug+rwx  storage  /path/to/your/app/public/images/icon

      - sudo chmod -R ug+rwx  storage  /path/to/your/app/public/images/logos
      
      - sudo chmod -R ug+rwx  storage  /path/to/your/app/public/images/map
      
      - sudo chmod -R ug+rwx  storage  /path/to/your/app/public/images/support
      
      - sudo chmod -R ug+rwx  storage  /path/to/your/app/public/images/users
      
      - sudo chmod -R ug+rwx  storage  /path/to/your/app/public/images/vehicle

## Rename Some file
    LINUX
      - cd www
      - cp index.php.local.docker index.php
      - cp .htaccess.local.docker .htaccess
      - cd public
      - cp index.php.local.docker index.php
      - cp .htaccess.local.docker .htaccess

    WINDOWS
      - root folder
      - copy index.php.local.docker index.php
      - copy .htaccess.local.docker .htaccess
      - cd public
      - copy index.php.local.docker index.php
      - copy .htaccess.local.docker .htaccess

## Base URL

This is the current Base URL

    http://localhost:5001/www
    
    
## Now login with

Super Admin: 

    http://localhost:5001/www/admin
    admin / 12345678
    
# Note: Check GD library installed. Try to upload a image.
