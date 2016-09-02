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

    public function testItAddsDefaultAttributes()
    {
        $this->assertEquals($_SERVER['HTTP_USER_AGENT'], $this->query->get('useragent'));
        $this->assertEquals($_SERVER['REMOTE_ADDR'], $this->query->get('userip'));
        $this->assertEquals('2', $this->query->get('v'));
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

    public function testItReturnsFalseIfRequiredAttributesMissing()
    {
        $this->assertFalse($this->query->isValid());
    }

    public function testItReturnsTrueIfRequiredAttributesPresent()
    {
        $this->query->set('publisher', uniqid());

        $this->assertTrue($this->query->isValid());
    }

    public function testItCanAddAttributesToUrl()
    {
        $url = $this->query->getUrl();
        $this->assertContains('v=', $url);
        $this->assertContains('userip=', $url);
        $this->assertContains('useragent=', $url);
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenSettingInvalidAttribute()
    {
        $this->query->set(uniqid(), uniqid());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenGettingInvalidAttribute()
    {
        $this->query->get(uniqid());
    }

    public function testItSetsAndGetsValidAttributes()
    {
        $attributes = [
            'q' => uniqid(),
            'l' => uniqid(),
            'publisher' => uniqid(),
            'highlight' => uniqid(),
        ];

        foreach ($attributes as $key => $value) {
            $this->query->set($key, $value);
        }

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $this->query->get($key));
        }
    }
}
