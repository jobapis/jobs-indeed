# Indeed Jobs Client

[![Latest Version](https://img.shields.io/github/release/JobBrander/jobs-indeed.svg?style=flat-square)](https://github.com/JobBrander/jobs-indeed/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/JobBrander/jobs-indeed/master.svg?style=flat-square&1)](https://travis-ci.org/JobBrander/jobs-indeed)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/JobBrander/jobs-indeed.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-indeed/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/JobBrander/jobs-indeed.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-indeed)
[![Total Downloads](https://img.shields.io/packagist/dt/jobbrander/jobs-indeed.svg?style=flat-square)](https://packagist.org/packages/jobbrander/jobs-indeed)

This package provides [Indeed Jobs API](https://ads.indeed.com/jobroll/xmlfeed)
support for the JobBrander's [Jobs Client](https://github.com/JobBrander/jobs-common).

## Installation

To install, use composer:

```
composer require jobbrander/jobs-indeed
```

## Usage

Usage is the same as Job Branders's Jobs Client, using `\JobBrander\Jobs\Client\Provider\Indeed` as the provider.

```php
$client = new JobBrander\Jobs\Client\Provider\Indeed([
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
    ->setPage(2)                                    // Start results at this result number, beginning with 0. Default is 0.
    ->setCount(200)                                 // Maximum number of results returned per query. Default is 10
    ->setDaysBack(10)                               // Number of days back to search.
    ->filterDuplicates(false)                       // Filter duplicate results. 0 turns off duplicate job filtering. Default is 1.
    ->includeLatLong(true)                          // If latlong=1, returns latitude and longitude information for each job result. Default is 0.
    ->setCountry('us')                              // Search within country specified. Default is us.
    ->setChannel('channel-one')                     // Channel Name: Group API requests to a specific channel
    ->setUserIp($_SERVER['REMOTE_ADDR'])            // The IP number of the end-user to whom the job results will be displayed.
    ->setUserAgent($_SERVER['HTTP_USER_AGENT'])     // The User-Agent (browser) of the end-user to whom the job results will be displayed.
    ->getJobs();
```

The `getJobs` method will return a [Collection](https://github.com/JobBrander/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/JobBrander/jobs-common/blob/master/src/Job.php) objects.

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

Please see [CONTRIBUTING](https://github.com/jobbrander/jobs-indeed/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/jobbrander/jobs-indeed/contributors)


## License

The Apache 2.0. Please see [License File](https://github.com/jobbrander/jobs-indeed/blob/master/LICENSE) for more information.
