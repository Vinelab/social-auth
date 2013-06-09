<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use Vinelab\Auth\Social\Network as SocialNetwork;

Class NetworkTest extends TestCase {

	public function setUp()
	{
		$this->settings = [
			'api_key'=>'someApiKey',
			'authentication_url' =>'http:://some.url.com',
			'client_id'    => 'hullaHoop',
			'redirect_uri' => 'somewhere over the rainbow',
			'permissions'        => 'up and down',
		];

		$this->mConfig = M::mock('Illuminate\Config\Repository');

		$this->mConfig->shouldReceive('get')
			->with('auth::social.facebook')
			->andReturn($this->settings);
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

	public function testSettings()
	{
		$network = new SocialNetwork('facebook', $this->mConfig);
		$this->assertEquals($this->settings, $network->settings());
	}

	public function testFetchingSingleSetting()
	{
		$network = new SocialNetwork('facebook', $this->mConfig);
		$this->assertEquals($this->settings['api_key'], $network->settings('api_key'));
		$this->assertEquals($this->settings['authentication_url'], $network->settings('authentication_url'));
	}
}