[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tonis-io/response-time/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/response-time/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tonis-io/response-time/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/response-time/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/tonis-io/response-time/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/response-time/build-status/master)

# Tonis\DoctrineORM

Tonis\DoctrineORM is a package to configure a simple Doctrine ORM EntityManager.

Composer
--------

```
composer require tonis-io/doctrine-orm
```

Usage
-----

```php
$app = new Tonis\App;
$app->package(new Tonis\DoctrineORM\Package($config));
```

Configuration
-------------

`Tonis\DoctrineORM` has the following config options available:

