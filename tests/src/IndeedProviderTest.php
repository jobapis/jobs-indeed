<?php namespace JobApis\Jobs\Client\Test;

use JobApis\Jobs\Client\Collection;
use JobApis\Jobs\Client\Job;
use JobApis\Jobs\Client\Providers\IndeedProvider;
use JobApis\Jobs\Client\Queries\IndeedQuery;
use Mockery as m;

class IndeedProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $_SERVER['HTTP_USER_AGENT'] = uniqid();
        $_SERVER['REMOTE_ADDR'] = uniqid();

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
        $this->assertEquals('results', $this->client->getListingsPath());
    }

    public function testItCanCreateJobObjectFromPayload()
    {
        $payload = $this->createJobArray();

        $results = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $results);
        $this->assertEquals($payload['jobtitle'], $results->getTitle());
        $this->assertEquals($payload['jobtitle'], $results->getName());
        $this->assertEquals($payload['snippet'], $results->getDescription());
        $this->assertEquals($payload['company'], $results->getCompanyName());
        $this->assertEquals($payload['url'], $results->getUrl());
        $this->assertEquals($payload['jobkey'], $results->getSourceId());
        $this->assertEquals($payload['formattedLocation'], $results->getLocation());
        $this->assertEquals($payload['latitude'], $results->getLatitude());
        $this->assertEquals($payload['longitude'], $results->getLongitude());
    }

    /**
     * Integration test for the client's getJobs() method.
     */
    public function testItCanGetJobs()
    {
        $url = 'http://api.indeed.com/ads/apisearch';

        $options = [
            'q' => uniqid(),
            'l' => uniqid(),
            'publisher' => uniqid(),
        ];

        $guzzle = m::mock('GuzzleHttp\Client');

        $query = new IndeedQuery($options);

        $client = new IndeedProvider($query);

        $client->setClient($guzzle);

        $response = m::mock('GuzzleHttp\Message\Response');

        $jobObjects = [
            (object) $this->createJobArray(),
            (object) $this->createJobArray(),
            (object) $this->createJobArray(),
        ];

        $jobs = json_encode((object) [
            'results' => $jobObjects
        ]);

        $guzzle->shouldReceive('get')
            ->with($query->getUrl(), [])
            ->once()
            ->andReturn($response);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn($jobs);

        $results = $client->getJobs();

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(count($jobObjects), $results);
    }

    /**
     * Integration test with actual API call to the provider.
     */
    public function testItCanGetJobsFromApi()
    {
        if (!getenv('PUBLISHER')) {
            $this->markTestSkipped('PUBLISHER not set. Real API call will not be made.');
        }

        $keyword = 'engineering';

        $query = new IndeedQuery([
            'q' => $keyword,
            'publisher' => getenv('PUBLISHER'),
            'latlong' => 1,
        ]);

        $client = new IndeedProvider($query);

        $results = $client->getJobs();

        $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);

        foreach($results as $job) {
            $this->assertEquals($keyword, $job->query);
        }
    }

    private function createJobArray()
    {
        $formattedLocation = uniqid().', '.uniqid();
        return [
            'jobtitle' => uniqid(),
            'company' => uniqid(),
            'formattedLocation' => $formattedLocation,
            'formattedLocationFull' => $formattedLocation.' '.uniqid(),
            'snippet' => uniqid(),
            'date' => '2015-07-'.rand(1, 31),
            'url' => uniqid(),
            'jobkey' => uniqid(),
            'latitude' => uniqid(),
            'longitude' => uniqid(),
        ];
    }
}
