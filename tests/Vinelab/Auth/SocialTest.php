<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;
use Vinelab\Auth\Social;

Class SocialTest extends TestCase {

	public function setUp()
	{
		$this->service = 'facebook';

		$this->apiKey = 'someApiKey';
		$this->redirectURI = 'comeBack';
		$this->authenticationUrl = '123';
		$this->permissions = 'permissions';

		$this->stateCachePrefix = 'social:auth:state:';

		$this->settings = [
			'api_key'            => $this->apiKey,
			'redirect_uri'       => $this->redirectURI,
			'authentication_url' => $this->authenticationUrl,
			'permissions'        => $this->permissions,
			'token_url'          => 'http://token.url.net',
			'secret'             => 'Cannot tell ya',
			'api_url'			 => 'http://api.url.com',
			'profile_uri'		 => '/me'
		];

		$this->mConfig     = M::mock('Illuminate\Config\Repository');
		$this->mConfig->shouldReceive('get')->andReturn($this->settings);

		$this->mCache      = M::mock('Illuminate\Cache\CacheManager');
		$this->mCache->shouldReceive('put')->andReturn(true);

		$this->mRedirector = M::mock('Illuminate\Routing\Redirector');
		$this->mRedirector->shouldReceive('to')->andReturn($this->mRedirector);

		$this->mResponse   = M::mock('Vinelab\Http\Response');

		$this->mHttpClient = M::mock('Vinelab\Http\Client');
		$this->mHttpClient->shouldReceive('get')->andReturn($this->mResponse);

		$this->mUserRepositoryInterface = M::mock('Vinelab\Auth\Contracts\UserRepositoryInterface');
		$this->mSocialAccountRepositoryInterface = M::mock('Vinelab\Auth\Contracts\SocialAccountRepositoryInterface');
	}

	public function testInstantiation()
	{
		$social = new Social($this->mConfig,
							 $this->mCache,
							 $this->mRedirector,
							 $this->mHttpClient,
							 $this->mUserRepositoryInterface,
							 $this->mSocialAccountRepositoryInterface);

		$this->assertInstanceOf('Vinelab\Auth\Social', $social);
	}

	public function testMakeState()
	{
		$social = new Social($this->mConfig,
							 $this->mCache,
							 $this->mRedirector,
							 $this->mHttpClient,
							 $this->mUserRepositoryInterface,
							 $this->mSocialAccountRepositoryInterface);
		$this->assertNotNull($social->makeState());
	}

	public function testAuthenticationGeneratesState()
	{
		$apiKey      = 'someApiKey';
		$redirectURI = 'someRedirectURI';
		$state       = 'aFakeState';

		$this->mCache->shouldReceive('put')->once();
		$social = new Social($this->mConfig,
							 $this->mCache,
							 $this->mRedirector,
							 $this->mHttpClient,
							 $this->mUserRepositoryInterface,
							 $this->mSocialAccountRepositoryInterface);
		$social->authenticate($this->service, $apiKey, $redirectURI);

		$this->assertNotNull($social->state);
	}

	public function testAuthentication()
	{
		$state = 'aFakeState';

		$this->mCache->shouldReceive('put')
			->with($state, ['api_key'=>$this->apiKey, 'redirect_uri'=>$this->redirectURI], 5)
			->once();

		$social = new Social($this->mConfig,
							 $this->mCache,
							 $this->mRedirector,
							 $this->mHttpClient,
							 $this->mUserRepositoryInterface,
							 $this->mSocialAccountRepositoryInterface);
		// IMPORTANT! This is put here for testing purposes ONLY, though should never be done this way
		$social->state = $state;
		$authenticate = $social->authenticate($this->service);

		$this->assertNotNull($authenticate);
		$this->assertInstanceOf('\Illuminate\Routing\Redirector', $authenticate);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AuthenticationException
	 */
	public function testAuthenticationCallbackWithoutState()
	{
		$s = new Social($this->mConfig,
						$this->mCache,
						$this->mRedirector,
						$this->mHttpClient,
						$this->mUserRepositoryInterface,
						$this->mSocialAccountRepositoryInterface);
		$s->authenticationCallback($this->service, ['api_key'=>'something']);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\SocialAccountException
	 */
	public function testAuthenticationCallback()
	{
		$state = 'aFakeState';

		$this->mCache->shouldReceive('get')
			->with($this->stateCachePrefix.$state)
			->andReturn(['api_key'=>$this->apiKey, 'redirect_uri'=>$this->redirectURI]);
		$this->mCache->shouldReceive('has')->andReturn(true);

		$this->mResponse->shouldReceive('json')->andReturn();
		$this->mResponse->shouldReceive('content')->andReturn('access_token=123&expires=1234');

		$s = new Social($this->mConfig,
						$this->mCache,
						$this->mRedirector,
						$this->mHttpClient,
						$this->mUserRepositoryInterface,
						$this->mSocialAccountRepositoryInterface);
		$this->assertEquals(['state'=>$state], $s->authenticationCallback($this->service, ['state'=>$state, 'code'=>'123', 'api_key'=>$this->apiKey]));
	}

	public function testSaveUser()
	{
		$id = 'service_id';
		$email = 'some@mail.net';
		$accessToken = 'some_really_long_access_token_blah';

		$mUser = M::mock('Vinelab\Auth\Contracts\UserEntityInterface');
		$mUser->id = 'user_id';
		$mUser->shouldReceive('getAttribute')->with('id')->andReturn('user_id');

		$this->mUserRepositoryInterface->shouldReceive('findByEmail')->once()->with($email)->andReturn([]);
		$this->mUserRepositoryInterface->shouldReceive('fillAndSave')->once()->andReturn($mUser);

		$this->mSocialAccountRepositoryInterface->shouldReceive('create')->once()
										->with($this->service, $id, 'user_id', $accessToken);

		$s = new Social($this->mConfig,
						 $this->mCache,
						 $this->mRedirector,
						 $this->mHttpClient,
						 $this->mUserRepositoryInterface,
						 $this->mSocialAccountRepositoryInterface);

		$mNetwork = M::mock('Vinelab\Auth\Social\Network');
		$s->_Network = $mNetwork;
		$s->_Network->name = $this->service;

		$saveUser = static::getProtectedMethod('saveUser', $s);

		$profile = (object) [
			'id' => $id,
			'email' => $email,
			'access_token'=> $accessToken
		];

		$network = M::mock('Vinelab\Auth\Social\Network');
		$network->name = $this->service;
		$s->network = $network;
		$saveUser->invokeArgs($s, [$profile]);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\SocialAccountException
	 */
	public function testSaveUserWithIdioticProfile()
	{
		$s = new Social($this->mConfig,
						$this->mCache,
						$this->mRedirector,
						$this->mHttpClient,
						$this->mUserRepositoryInterface,
						$this->mSocialAccountRepositoryInterface);

		$saveUser = static::getProtectedMethod('saveUser', $s);

		$profile = (object) [];
		$saveUser->invokeArgs($s, [$profile]);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\SocialAccountException
	 */
	public function testSaveUserWithNoProfile()
	{
		$s = new Social($this->mConfig,
						$this->mCache,
						$this->mRedirector,
						$this->mHttpClient,
						$this->mUserRepositoryInterface,
						$this->mSocialAccountRepositoryInterface);

		$saveUser = static::getProtectedMethod('saveUser', $s);
		$saveUser->invokeArgs($s, [null]);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AuthenticationException
	 */
	public function testAuthenticationCallbackWithInexistingState()
	{
		$state = 'aFakeState';
		$this->mCache->shouldReceive('has')->andReturn(false);
		$s = new Social($this->mConfig,
						$this->mCache,
						$this->mRedirector,
						$this->mHttpClient,
						$this->mUserRepositoryInterface,
						$this->mSocialAccountRepositoryInterface);

		$s->authenticationCallback($this->service, ['state'=>$state, 'code'=>'123', 'api_key'=>$this->apiKey]);
	}

	protected static function getProtectedMethod($name, $class)
	{
		$class = new \ReflectionClass(get_class($class));
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

}