<?php namespace JobApis\Jobs\Client\Providers\Test;

use JobApis\Jobs\Client\Queries\IndeedQuery;
use Mockery as m;

class IndeedQueryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Set up server variables for testing
        $_SERVER['HTTP_USER_AGENT'] = uniqid();
        $_SERVER['REMOTE_ADDR'] = uniqid();

        $this->query = new IndeedQuery();
    }

    public function testItCanGetBaseUrl()
    {
        $this->assertEquals(
            'http://api.indeed.com/ads/apisearch',
            $this->query->getBaseUrl()
        );
    }

    public function testItCanGetKeyword()
    {
        $keyword = uniqid();
        $this->query->set('q', $keyword);
        $this->assertEquals($keyword, $this->query->getKeyword());
    }
}
