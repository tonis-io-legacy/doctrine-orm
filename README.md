[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tonis-io/doctrine-orm/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/doctrine-orm/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tonis-io/doctrine-orm/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/doctrine-orm/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/tonis-io/doctrine-orm/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/doctrine-orm/build-status/master)

# Tonis\DoctrineORM

Tonis\DoctrineORM is a package to configure a simple Doctrine ORM EntityManager.

## Composer

```
composer require tonis-io/doctrine-orm
```

## Usage

```php
$entityManager = Tonis\DoctrineORM\EntityManagerFactory::create($config, $container)
```

**Note:** Container is optional and is used to pull the cache from if the cache is present as a string.

## Configuration

The `create()` method accepts an array of of configuration. The following is an example
with default values.

```php
[
    'debug'      => true,
    'cache'      => null,
    'proxy_dir'  => null,
    'driver'     => [
        'type'   => self::DRIVER_ANNOTATION,
        'simple' => true,
        'paths'  => [],
    ],
    'connection' => [
        'driver' => 'pdo_mysql'
    ],
];
```
