<?php namespace JobBrander\Jobs\Client\Providers\Test;

use JobBrander\Jobs\Client\Providers\Indeed;
use Mockery as m;

class IndeedTest extends \PHPUnit_Framework_TestCase
{
    private $clientClass = 'JobBrander\Jobs\Client\Providers\AbstractProvider';
    private $collectionClass = 'JobBrander\Jobs\Client\Collection';
    private $jobClass = 'JobBrander\Jobs\Client\Job';

    public function setUp()
    {
        $this->params = [
            'publisherId' => '12345667',
            'version' => 2,
            'highlight' => 0,
        ];
        $this->client = new Indeed($this->params);
    }

    private function getResultItems($count = 1)
    {
        $results = [];

        for ($i = 0; $i < $count; $i++) {
            array_push($results, [
                'jobtitle' => uniqid(),
                'company' => uniqid(),
                'formattedLocation' => uniqid(),
                'source' => uniqid(),
                'date' => '2015-07-'.rand(1,31),
                'snippet' => uniqid(),
                'url' => uniqid(),
                'jobkey' => uniqid(),
            ]);
        }

        return $results;
    }

    public function testItWillUseJsonFormat()
    {
        $format = $this->client->getFormat();

        $this->assertEquals('json', $format);
    }

    public function testItWillUseGetHttpVerb()
    {
        $verb = $this->client->getVerb();

        $this->assertEquals('GET', $verb);
    }

    public function testListingPath()
    {
        $path = $this->client->getListingsPath();

        $this->assertEquals('results', $path);
    }

    public function testItWillProvideEmptyParameters()
    {
        $parameters = $this->client->getParameters();

        $this->assertEmpty($parameters);
        $this->assertTrue(is_array($parameters));
    }

    public function testUrlIncludesHighlightWhenProvided()
    {
        $param = 'highlight='.$this->params['highlight'];

        $url = $this->client->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesHighlightWhenNotProvided()
    {
        $param = 'highlight=';

        $url = $this->client->setHighlight(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesKeywordWhenProvided()
    {
        $keyword = uniqid().' '.uniqid();
        $param = 'q='.urlencode($keyword);

        $url = $this->client->setKeyword($keyword)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesKeywordWhenNotProvided()
    {
        $param = 'q=';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesLocationWhenCityAndStateProvided()
    {
        $city = uniqid();
        $state = uniqid();
        $param = 'l='.urlencode($city.', '.$state);

        $url = $this->client->setCity($city)->setState($state)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesLocationWhenCityProvided()
    {
        $city = uniqid();
        $param = 'l='.urlencode($city);

        $url = $this->client->setCity($city)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesLocationWhenStateProvided()
    {
        $state = uniqid();
        $param = 'l='.urlencode($state);

        $url = $this->client->setState($state)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesLocationWhenNotProvided()
    {
        $param = 'l=';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesLimitWhenProvided()
    {
        $limit = uniqid();
        $param = 'limit='.$limit;

        $url = $this->client->setCount($limit)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesLimitWhenNotProvided()
    {
        $param = 'limit=';

        $url = $this->client->setCount(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesPublisherWhenProvided()
    {
        $param = 'publisher='.$this->params['publisherId'];

        $url = $this->client->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesPublisherWhenNotProvided()
    {
        $param = 'publisher=';

        $url = $this->client->setPublisherId(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesStartWhenProvided()
    {
        $page = uniqid();
        $param = 'start='.$page;

        $url = $this->client->setPage($page)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesStartWhenNotProvided()
    {
        $param = 'start=';

        $url = $this->client->setPage(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesVersionWhenProvided()
    {
        $param = 'v='.$this->params['version'];

        $url = $this->client->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesVersionWhenNotProvided()
    {
        $param = 'v=';

        $url = $this->client->setVersion(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testItCanCreateJobFromPayload()
    {
        $payload = $this->createJobArray();
        $results = $this->client->createJobObject($payload);

        $this->assertEquals($payload['jobtitle'], $results->getTitle());
        $this->assertEquals($payload['jobtitle'], $results->getName());
        $this->assertEquals($payload['snippet'], $results->getDescription());
        $this->assertEquals($payload['company'], $results->getCompanyName());
        $this->assertEquals($payload['url'], $results->getUrl());
        $this->assertEquals($payload['jobkey'], $results->getSourceId());
        $this->assertEquals($payload['formattedLocation'], $results->getLocation());
    }

    public function testItCanConnect()
    {
        $provider = $this->getProviderAttributes();

        for ($i = 0; $i < $provider['jobs_count']; $i++) {
            $payload['results'][] = $this->createJobArray();
        }

        $responseBody = json_encode($payload);

        $job = m::mock($this->jobClass);
        $job->shouldReceive('setQuery')->with($provider['keyword'])
            ->times($provider['jobs_count'])->andReturnSelf();
        $job->shouldReceive('setSource')->with($provider['source'])
            ->times($provider['jobs_count'])->andReturnSelf();

        $response = m::mock('GuzzleHttp\Message\Response');
        $response->shouldReceive('getBody')->once()->andReturn($responseBody);

        $http = m::mock('GuzzleHttp\Client');
        $http->shouldReceive(strtolower($this->client->getVerb()))
            ->with($this->client->getUrl(), $this->client->getHttpClientOptions())
            ->once()
            ->andReturn($response);
        $this->client->setClient($http);

        $results = $this->client->getJobs();

        $this->assertInstanceOf($this->collectionClass, $results);
        $this->assertCount($provider['jobs_count'], $results);
    }

    private function createJobArray() {
        return [
            'jobtitle' => uniqid(),
            'company' => uniqid(),
            'formattedLocation' => uniqid().', '.uniqid(),
            'snippet' => uniqid(),
            'date' => '2015-07-'.rand(1,31),
            'url' => uniqid(),
            'jobkey' => uniqid(),
        ];
    }

    private function getProviderAttributes($attributes = [])
    {
        $defaults = [
            'path' => uniqid(),
            'format' => 'json',
            'keyword' => uniqid(),
            'source' => uniqid(),
            'params' => [uniqid()],
            'jobs_count' => rand(2,10),

        ];
        return array_replace($defaults, $attributes);
    }
}
