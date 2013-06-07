<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use Vinelab\Auth\Social\Network as SocialNetwork;

Class NetworkTest extends TestCase {

	public function setUp()
	{
		$this->mConfig = M::mock('Illuminate\Config\Repository');
		$this->mConfig->shouldReceive('get')->andReturn(true);
	}

	public function testInstantiationWithService()
	{
		$network = new SocialNetwork('facebook', $this->mConfig);
		$this->assertInstanceOf('Vinelab\Auth\Social\Network', $network);
		$this->assertInstanceOf('Vinelab\Auth\Social\Networks\Facebook', $network->service, 'Should have instantiated a Facebook instance as a service');
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\SocialNetworkNotSupportedException
	 */
	public function testUnsupportedService()
	{
		$network = new SocialNetwork('nothing', $this->mConfig);
	}

	public function testAuthenticationURL()
	{
		$this->assertNotNull((new SocialNetwork('facebook', $this->mConfig))->authenticationURL());
	}
}