<?php namespace JobBrander\Jobs\Client\Providers\Test;

use JobBrander\Jobs\Client\Providers\Indeed;
use Mockery as m;

class IndeedTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->params = [
            'publisherId' => '3806336598146294',
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
                'date' => uniqid(),
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

    public function testItCanConnect()
    {
        $jobCount = rand(2,10);
        $listings = ['results' => $this->getResultItems($jobCount)];
        $source = $this->client->getSource();
        $keyword = 'project manager';
        $city = 'Chicago';
        $state = 'IL';

        $this->client->setKeyword($keyword)
            ->setCity($city)
            ->setState($state);

        $response = m::mock('GuzzleHttp\Message\Response');
        $response->shouldReceive($this->client->getFormat())->once()->andReturn($listings);

        $http = m::mock('GuzzleHttp\Client');
        $http->shouldReceive(strtolower($this->client->getVerb()))
            ->with($this->client->getUrl(), $this->client->getHttpClientOptions())
            ->once()
            ->andReturn($response);
        $this->client->setClient($http);

        $results = $this->client->getJobs();

        $this->assertCount($jobCount, $results);

        foreach ($listings['results'] as $i => $result) {
            $this->assertEquals($listings['results'][$i]['jobtitle'], $results->get($i)->title);
            $this->assertEquals($listings['results'][$i]['company'], $results->get($i)->company);
            $this->assertEquals($listings['results'][$i]['formattedLocation'], $results->get($i)->location);
            $this->assertEquals($listings['results'][$i]['snippet'], $results->get($i)->description);
            $this->assertEquals($listings['results'][$i]['url'], $results->get($i)->url);
            $this->assertEquals($listings['results'][$i]['jobkey'], $results->get($i)->id);
            $this->assertEquals($source, $results->get($i)->source);
            $this->assertEquals($keyword, $results->get($i)->query);
        }
    }
}
