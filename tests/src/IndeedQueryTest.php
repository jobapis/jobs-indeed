<?php namespace JobApis\Jobs\Client\Providers\Test;

use JobApis\Jobs\Client\Queries\IndeedQuery;
use Mockery as m;

class IndeedQueryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->query = new IndeedQuery();
    }
}
