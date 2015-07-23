<?php

namespace Vinelab\Auth\Tests\Cache;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;
use Vinelab\Auth\Cache\Store;

class StoreTest extends TestCase
{
    public function setUp()
    {
        $this->cache = M::mock('Illuminate\Cache\CacheManager');

        $this->store = new Store($this->cache);
    }

    public function test_storing_default()
    {
        $this->cache->shouldReceive('put')
            ->with('arnold', 'layne', 2)
            ->once()->andReturn(null);

        $this->store->put('arnold', 'layne');
    }

    public function test_storing_with_duration()
    {
        $this->cache->shouldReceive('put')
            ->with('mickey', 'mouse', 10)
            ->once()->andReturn(null);

        $this->store->put('mickey', 'mouse', 10);
    }

    public function test_retrieving()
    {
        $this->cache->shouldReceive('get')
            ->with('arnold')
            ->once()->andReturn('layne');

        $this->assertEquals('layne', $this->store->get('arnold'));
    }

    public function test_checking_value_in_cache()
    {
        $this->cache->shouldReceive('has')
            ->with('arnold')->once()->andReturn(true);

        $this->assertTrue($this->store->has('arnold'));
    }
}
