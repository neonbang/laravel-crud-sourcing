# Watch and report on CRUD events within your Laravel application

> Do NOT use this in production yet. We are still testing internally on some projects. We'll keep you posted.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/neonbang/laravel-crud-sourcing.svg?style=flat-square)](https://packagist.org/packages/neonbang/laravel-crud-sourcing)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/neonbang/laravel-crud-sourcing/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/neonbang/laravel-crud-sourcing/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/neonbang/laravel-crud-sourcing/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/neonbang/laravel-crud-sourcing/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/neonbang/laravel-crud-sourcing.svg?style=flat-square)](https://packagist.org/packages/neonbang/laravel-crud-sourcing)

## Installation

You can install the package via composer:

```bash
composer require neonbang/laravel-crud-sourcing
```

You can publish the configuration and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-crud-sourcing-config"
php artisan migrate
```

You can publish just the config file with:

```bash
php artisan vendor:publish --tag="laravel-crud-sourcing-config"
```

This is the contents of the published config file:

```php
return [
    'model_metadata_map' => [],
];
```

## Usage

Coming soon...

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Matt Riggio](https://github.com/neonbang)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
