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

Usage is the same as JobApis' Jobs Client, using `\JobApis\Jobs\Client\Providers\Indeed` as the provider.

Any of the parameters documented in Indeed's documentation can be used by appending "set" to them. For example, `setQ('query')` would allow you to set the query for an API call. Alternatively, we offer the shortcut methods listed below.

```php
$client = new JobApis\Jobs\Client\Provider\Indeed([
    'publisher' => 'YOUR INDEED PUBLISHER ID',
    'v' => 2, // Optional. Default is 2.
    'highlight' => 0,
]);

$jobs = $client
    ->setKeyword('project manager')                 // Query. By default terms are ANDed. To see what is possible, use the [advanced search page](http://www.indeed.com/advanced_search) to perform a search and then check the url for the q value.
    ->setFormat('json')                             // Format. Which output format of the API you wish to use. The options are "xml" and "json". If omitted or invalid, the json format is used.
    ->setLocation('Chicago, IL')                    // Location. Use a postal code or a "city, state/province/region" combination.
    ->setSort('date')                               // Sort by relevance or date. Default is relevance.
    ->setRadius('100')                              // Distance from search location ("as the crow flies"). Default is 25.
    ->setSiteType('jobsite')                        // Site type. To show only jobs from job boards use "jobsite". For jobs from direct employer websites use "employer".
    ->setJobType('fulltime')                        // Job type. Allowed values: "fulltime", "parttime", "contract", "internship", "temporary".
    ->setStart(2)                                   // Start results at this result number, beginning with 0. Default is 0.
    ->setLimit(200)                                 // Maximum number of results returned per query. Default is 10
    ->setDaysBack(10)                               // Number of days back to search.
    ->setFilterDuplicates(false)                    // Filter duplicate results. 0 turns off duplicate job filtering. Default is 1.
    ->setIncludeLatLong(true)                       // If latlong=1, returns latitude and longitude information for each job result. Default is 0.
    ->setCountry('us')                              // Search within country specified. Default is us.
    ->setChnl('channel-one')                        // Channel Name: Group API requests to a specific channel
    ->setUserIp($_SERVER['REMOTE_ADDR'])            // The IP number of the end-user to whom the job results will be displayed.
    ->setUserAgent($_SERVER['HTTP_USER_AGENT'])     // The User-Agent (browser) of the end-user to whom the job results will be displayed.
    ->getJobs();
```

The `getJobs` method will return a [Collection](https://github.com/jobapis/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/jobapis/jobs-common/blob/master/src/Job.php) objects.

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
