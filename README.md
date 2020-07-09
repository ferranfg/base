# 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ferranfg/base.svg?style=flat-square)](https://packagist.org/packages/ferranfg/base)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/ferranfg/base/run-tests?label=tests)](https://github.com/ferranfg/base/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/ferranfg/base.svg?style=flat-square)](https://packagist.org/packages/ferranfg/base)

## Installation

You can install the package via composer:

```bash
composer require ferranfg/base
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Ferranfg\Base\BaseServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Ferranfg\Base\BaseServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

``` php
$base = new Ferranfg\Base();
echo $base->echoPhrase('Hello, Ferranfg!');
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email hola@ferranfigueredo.com instead of using the issue tracker.

## Credits

- [Ferran Figueredo](https://github.com/ferranfg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
