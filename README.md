# AGAK CORE

A core package for AGAK Platform.  

List of features:  
1. Activity logger  
2. API

## Installation

Install this package via composer:  

```
composer require mhafizhasan/agak-core
```  

Install the service provider and register aliases:

```
// config/app.php
'providers' => [
    ...
    Mhafizhasan\AgakCore\AgakCoreServiceProvider::class,
];

'aliases' => [
    ...
    'AgakLogger' => Mhafizhasan\AgakCore\Facade\AgakLogger::class,
    'AgakAPI' => Mhafizhasan\AgakCore\Facade\AgakAPI::class,
];
```

Publish the migration:

```
php artisan vendor:publish --provider="Mhafizhasan\AgakCore\AgakCoreServiceProvider" --tag="migrations"
```

Run the migration:

```
php artisan migrate
```
