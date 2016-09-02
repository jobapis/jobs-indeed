# Indeed Jobs Client

[![Latest Version](https://img.shields.io/github/release/jobapis/jobs-indeed.svg?style=flat-square)](https://github.com/jobapis/jobs-indeed/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/jobapis/jobs-indeed/master.svg?style=flat-square&1)](https://travis-ci.org/jobapis/jobs-indeed)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/jobapis/jobs-indeed.svg?style=flat-square)](https://scrutinizer-ci.com/g/jobapis/jobs-indeed/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/jobapis/jobs-indeed.svg?style=flat-square)](https://scrutinizer-ci.com/g/jobapis/jobs-indeed)
[![Total Downloads](https://img.shields.io/packagist/dt/jobapis/jobs-indeed.svg?style=flat-square)](https://packagist.org/packages/jobapis/jobs-indeed)

This package provides [Indeed Jobs API](https://ads.indeed.com/jobroll/xmlfeed)
support for the [Jobs Common Project](https://github.com/jobapis/jobs-common).

## Installation

To install, use composer:

```
composer require jobapis/jobs-indeed
```

## Usage

Create a Query object and add all the parameters you'd like via the constructor.
 
```php
// Add parameters to the query via the constructor
$query = new JobApis\Jobs\Client\Queries\IndeedQuery([
    'publisher' => YOUR_PUBLISHER_ID
]);
```

Or via the "set" method. All of the parameters documented in Indeed's documentation can be added.

```php
// Add parameters via the set() method
$query->set('q', 'engineering');
```

You can even chain them if you'd like.

```php
// Add parameters via the set() method
$query->set('l', 'Chicago, IL')
    ->set('highlight', '1')
    ->set('latlong', '1');
```
 
Then inject the query object into the provider.

```php
// Instantiating an IndeedProvider with a query object
$client = new JobApis\Jobs\Client\Provider\IndeedProvider($query);
```

And call the "getJobs" method to retrieve results.

```php
// Get a Collection of Jobs
$jobs = $client->getJobs();
```

This will return a [Collection](https://github.com/jobapis/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/jobapis/jobs-common/blob/master/src/Job.php) objects.

## Testing

To run all tests except for actual API calls
``` bash
$ ./vendor/bin/phpunit
```

To run all tests including actual API calls
``` bash
$ PUBLISHER=<YOUR PUBLISHER ID> ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/jobapis/jobs-indeed/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [Karl Hughes](https://github.com/karllhughes)
- [All Contributors](https://github.com/jobapis/jobs-indeed/contributors)


## License

The Apache 2.0. Please see [License File](https://github.com/jobapis/jobs-indeed/blob/master/LICENSE) for more information.
