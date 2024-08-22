> Do NOT use this in production yet. We are still testing internally on some projects. We'll keep you posted.

# Watch and report on your Laravel CRUD events

[![Latest Version on Packagist](https://img.shields.io/packagist/v/neonbang/laravel-crud-sourcing.svg?style=flat-square)](https://packagist.org/packages/neonbang/laravel-crud-sourcing)
[![Total Downloads](https://img.shields.io/packagist/dt/neonbang/laravel-crud-sourcing.svg?style=flat-square)](https://packagist.org/packages/neonbang/laravel-crud-sourcing)

What problem are we solving here?

> We were getting sick of adding columns to database tables that didn't really *fit* (something like `last_notification_sent_without_response`). We also were getting tired of creating report queries that were slow.

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
