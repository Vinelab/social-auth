<?php namespace Vinelab\Auth\Tests\Social;

use Mockery as M;
use PHPUnit_Framework_TestCase as TestCase;

use Vinelab\Auth\Social\Provider;

class TestableProvider extends Provider
{
    protected $name = 'fake';

    protected $mandatory = ['setting'];

    public function authenticate() {}

    public function callback($input) {}
}

class ProviderTest extends TestCase {

    public function setUp()
    {
        $this->config = M::mock('Illuminate\Config\Repository');
    }

    public function test_getting_settings()
    {
        $this->config->shouldReceive('get')->once()
            ->with('social.fake')
            ->andReturn(['setting'=>'value']);

        $this->provider = new TestableProvider($this->config);

        $this->assertInstanceOf('Vinelab\Auth\Social\Provider', $this->provider);
        $this->assertEquals(['setting'=>'value'], $this->provider->settings());
    }

    /**
     * @depends test_getting_settings
     * @expectedException Vinelab\Auth\Exceptions\InvalidProviderSettingsException
     */
    public function test_fails_validating_settings()
    {
        $this->config->shouldReceive('get')->once()
            ->with('social.fake')
            ->andReturn(['setting'=>'value']);

        $this->provider = new TestableProvider($this->config);
        $validateSettings = static::getProtectedMethod('validateSettings', $this->provider);

        $validateSettings->invokeArgs($this->provider, ['something'=>'else']);
    }

    /**
     * @depends test_getting_settings
     */
    public function test_passes_validating_settings()
    {
        $this->config->shouldReceive('get')->once()
            ->with('social.fake')
            ->andReturn(['setting'=>'value']);

        $this->provider = new TestableProvider($this->config);
        $validateSettings = static::getProtectedMethod('validateSettings', $this->provider);

        $this->assertTrue($validateSettings->invokeArgs($this->provider, [['setting'=>'value']]));
    }

    protected static function getProtectedMethod($name, $class)
    {
        $class = new \ReflectionClass(get_class($class));
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}