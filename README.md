# cl-slack
A PHP script to read in and parse craigslist with parameters and post to slack channel

Requirements
* PHP
* Composer

Installation

Add to your composer.json, run php composer

{
    "require": {
        "maknz/slack": "^1.7",
        "andrewevansmith/php-craigslist-api-utility": "dev-master"
    }
}

Create schema craigslack and import private/schema.sql

Configure database connections in private/db.php
Configure slack connection in private/slack.php

test by running php cl2slack.php

