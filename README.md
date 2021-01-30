Geonames Bundle
===============
[![Build Status](https://travis-ci.org/bordeux/geoname-bundle.svg?branch=master)](https://travis-ci.org/bordeux/geoname-bundle) [![Coverage Status](https://coveralls.io/repos/github/bordeux/geoname-bundle/badge.svg?branch=master)](https://coveralls.io/github/bordeux/geoname-bundle?branch=master)[![Latest Stable Version](https://poser.pugx.org/bordeux/geoname-bundle/version)](https://packagist.org/packages/bordeux/geoname-bundle)

# CREDITS [IMPORTANT]

All the work was originaly done by Krzysztof Bednarczyk <krzysztof@bednarczyk.me>. This package is a fork to get a fast fix on error happening on
symfony 5.

# Introduction

Provides access to the data exported by [GeoNames.org][1] into  [Symfony 2][2] and [Symfony 3][2]
applications.


## What is [geonames.org][1]

From the geonames.org website:

> The GeoNames geographical database covers all countries and contains over
> eight million placenames that are available for download free of charge.


## When to use this bundle

Most useful application for this bundle is to normalize the geograhical
information stored in your database such as Countries, States and Cities. Thanks
to the extensive [geonames.org][1] data almost all cities, towns and suburbs are
covered worldwide.

## Features

- Imports the following geonames.org data:

    * Countries
    * Timezones
    * States & Provences
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
the [geonames.org][1] export (http://download.geonames.org/export/dump)

### Import data

Loads a list of all data from [geonames.org][1]

```sh
    php bin/console bordeux:geoname:import  --env=prod
```

### Options


```
Usage:
  bordeux:geoname:import [options]

Options:
  -a, --archive[=ARCHIVE]                   Archive to GeoNames [default: "http://download.geonames.org/export/dump/allCount
ries.zip"]
  -t, --timezones[=TIMEZONES]               Timezones file [default: "http://download.geonames.org/export/dump/timeZones.txt
"]
  -o, --download-dir[=DOWNLOAD-DIR]         Download dir
  -h, --help                                Display this help message
  -q, --quiet                               Do not output any message
  -V, --version                             Display this application version
      --ansi                                Force ANSI output
      --no-ansi                             Disable ANSI output
  -n, --no-interaction                      Do not ask any interactive question
  -e, --env=ENV                             The environment name [default: "dev"]
      --no-debug                            Switches off debug mode
  -a1, --admin1-codes[=ADMIN1-CODES]        Admin 1 Codes file [default: "http://download.geonames.org/export/dump/admin1Cod
esASCII.txt"]
  -a2, --admin2-codes[=ADMIN2-CODES]        Admin 2 Codes file [default: "http://download.geonames.org/export/dump/admin2Cod
es.txt"]
  -lc, --languages-codes[=LANGUAGES-CODES]  Admin 2 Codes file [default: "http://download.geonames.org/export/dump/iso-langu
agecodes.txt"]
  -v|vv|vvv, --verbose                      Increase the verbosity of messages: 1 for normal output, 2 for more verbose outp
ut and 3 for debug

Help:
  Import GeoNames

```

 [1]: http://geonames.org
 [2]: http://symfony.com