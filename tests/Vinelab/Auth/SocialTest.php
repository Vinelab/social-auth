<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;
use Vinelab\Auth\Social;

Class SocialTest extends TestCase {

	public function setUp()
	{
		$this->service = 'facebook';

		$this->mConfig     = M::mock('\Illuminate\Config\Repository');
		$this->mConfig->shouldReceive('get')->andReturn(true);

		$this->mCache      = M::mock('\Illuminate\Cache\CacheManager');
		$this->mResposne   = M::mock('\Illuminate\Http\Response');
		$this->mRedirector = M::mock('\Illuminate\Routing\Redirector');
		$this->mRedirector->shouldReceive('to')->andReturn(true);
	}

	public function testInstantiation()
	{
		$social = new Social($this->service, $this->mConfig, $this->mCache, $this->mResposne, $this->mRedirector);
		$this->assertInstanceOf('Vinelab\Auth\Social', $social);
		$this->assertInstanceOf('Vinelab\Auth\Social\Network', $social->network);
	}

	public function testMakeState()
	{
		$social = new Social($this->service, $this->mConfig, $this->mCache, $this->mResposne, $this->mRedirector);
		$this->assertNotNull($social->makeState());
	}

	public function testAuthenticationGeneratesState()
	{
		$apiKey      = 'someApiKey';
		$redirectURI = 'someRedirectURI';
		$state       = 'aFakeState';

		$this->mCache->shouldReceive('put')->once();
		$social = new Social($this->service, $this->mConfig, $this->mCache, $this->mResposne, $this->mRedirector);
		$social->authenticate($apiKey, $redirectURI);

		$this->assertNotNull($social->state);
	}

	public function testAuthentication()
	{
		$apiKey      = 'someApiKey';
		$redirectURI = 'someRedirectURI';
		$state       = 'aFakeState';

		$this->mCache->shouldReceive('put')->with($state, [
			'api_key'      => $apiKey,
			'redirect_uri' => $redirectURI
		])->once();

		$social = new Social($this->service, $this->mConfig, $this->mCache, $this->mResposne, $this->mRedirector);
		// IMPORTANT! This is put here for testing purposes ONLY, though should never be done this way
		$social->state = $state;

		$this->assertNotNull($social->authenticate($apiKey, $redirectURI));
	}
}