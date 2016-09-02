<?php namespace JobApis\Jobs\Client\Providers\Test;

use JobApis\Jobs\Client\Collection;
use JobApis\Jobs\Client\Job;
use JobApis\Jobs\Client\Providers\IndeedProvider;

use Mockery as m;

class IndeedProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->query = m::mock('JobApis\Jobs\Client\Queries\IndeedQuery');

        $this->client = new IndeedProvider($this->query);
    }

    public function testItCanGetDefaultResponseFields()
    {
        $fields = [
            'jobtitle',
            'company',
            'formattedLocation',
            'formattedLocationFull',
            'source',
            'date',
            'snippet',
            'url',
            'jobkey',
            'latitude',
            'longitude'
        ];
        $this->assertEquals($fields, $this->client->getDefaultResponseFields());
    }

    public function testItCanGetListingsPath()
    {
        $this->assertEquals('jobs', $this->client->getListingsPath());
    }

    public function testItCanCreateJobObjectFromPayload()
    {
        //
    }

    public function testItCanGetJobs()
    {
        //
    }

    public function testItCanGetJobsFromApi()
    {
        //
    }
}
