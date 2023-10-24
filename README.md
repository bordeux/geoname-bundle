Geonames Bundle
===============
![Build Status](https://github.com/bordeux/geoname-bundle/actions/workflows/ci.yml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/bordeux/geoname-bundle/version)](https://packagist.org/packages/bordeux/geoname-bundle)

# Introduction

Provides access to the data exported by [GeoNames.org][1] into  [Symfony 5, 6][2]
applications. Starting version 3.0 this library supporting only PostgreSQL database.


## What is [geonames.org][1]

From the geonames.org website:

> The GeoNames geographical database covers all countries and contains over
> eight million place names that are available for download free of charge.


## When to use this bundle

Most useful application for this bundle is to normalize the geograhical
information stored in your database such as Countries, States and Cities. Thanks
to the extensive [geonames.org][1] data almost all cities, towns and suburbs are
covered worldwide.

## Features

- Imports the following geonames.org data:

    * Countries
    * Timezones
    * States & Provinces
    * Cities, Towns, Suburbs, Villages etc.

- Provides the following data store implementations:

    * Doctrine ORM

# Installation

1. Install the bundle using composer:

    ```sh
    composer require bordeux/geoname-bundle
    ```


2. Add the bundle to your `AppKernel.php`

    ```php
    // AppKernel::registerBundles()
    $bundles = array(
        // ...
            new Bordeux\Bundle\GeoNameBundle\BordeuxGeoNameBundle(),
        // ...
    );

## Install or update database schema


Execute the migrations using the supplied migration configuration

```sh
    php bin/console doctrine:schema:update --force
```


## Import the data

**Note** that importing the data from the remote geonames.org repository involves downloading
almost 350 MB data from [geonames.org][1].

The following commands can be used in sequence to load all supported data from
the [geonames.org][1] export (https://download.geonames.org/export/dump)

### Import data

Loads a list of all data from [geonames.org][1]

```sh
    php bin/console bordeux:geoname:import  --env=prod
```

### How to run tests?

Just run: ``docker-compose -f docker-compose.tests.yml up``

 [1]: https://geonames.org
 [2]: https://symfony.com
