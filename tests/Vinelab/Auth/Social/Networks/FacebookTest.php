<?php namespace Vinelab\Authe\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use Vinelab\Auth\Social\Networks\Facebook as Facebook;

Class FacebookTest extends TestCase {

	public function setUp()
	{
		$this->settings = [

			'api_key'            => '1235oME4P17EY',
			'secret'             => 'AppSecret',
			'redirect_uri'       => 'http://redirect/here',
			'permissions'        => 'email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_website',
			'api_url'            => 'https://graph.facebook.com',
			'authentication_url' => 'https://www.facebook.com/dialog/oauth/',
			'token_url'          => 'https://graph.facebook.com/oauth/access_token',
			'profile_uri'        => '/me'
		];

		$this->mConfig = M::mock('Illuminate\Config\Repository');
		$this->mConfig->shouldReceive('get')
			->once('auth::social.facebook')
			->andReturn($this->settings);

		$this->mHttpClient = M::mock('Vinelab\Http\Client[get]');
	}

	public function testInstantiation()
	{
		$this->assertInstanceOf('Vinelab\Auth\Social\Networks\Facebook', new Facebook($this->mConfig, $this->mHttpClient), $this->mHttpClient);
	}

	public function testAuthenticationURL()
	{
		$f = new Facebook($this->mConfig, $this->mHttpClient);
		$url = $f->authenticationURL();

		$this->assertNotNull($url);

		$params = [
			'client_id'    => $this->settings['api_key'],
			'redirect_uri' => $this->settings['redirect_uri'],
			'scope'        => $this->settings['permissions']
		];

		$expectedURL = sprintf('%s?%s', $this->settings['authentication_url'], http_build_query($params));
		$this->assertEquals($expectedURL, $url, 'URL must be of this format');
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AuthenticationException
	 */
	public function testAuthenticationCallbackExceptionWithNull()
	{
		$f = new Facebook($this->mConfig, $this->mHttpClient);
		$f->authenticationCallback(null);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AuthenticationException
	 * @expectedExceptionMessage Authentication failed: access_denied - The user declined your app
	 */
	public function testAuthenticationCallbackWithError()
	{
		$f = new Facebook($this->mConfig, $this->mHttpClient);
		$f->authenticationCallback(['error'=>'access_denied', 'error_description'=>'The user declined your app']);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AuthenticationException
	 */
	public function testAuthenticationCallbackWithoutCode()
	{
		$f = new Facebook($this->mConfig, $this->mHttpClient);
		$f->authenticationCallback(['state'=>'123', 'api_key'=>'something else']);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AccessTokenException
	 */
	public function testAuthenticationCallbackWithErronousResponse()
	{
		$mResponse = M::mock('Vinelab\Http\Response');
		$mResponse->shouldReceive('json')->once()->andReturn(false);
		$mResponse->shouldReceive('content')->once()->andReturn('access_token');

		$this->mHttpClient->shouldReceive('get')->andReturn($mResponse);
		$f = new Facebook($this->mConfig, $this->mHttpClient);

		$f->authenticationCallback(['code'=>'abadish']);
	}

	public function testRequestAccessToken()
	{
		$returnedAccessToken = '123';
		$expires = '1234';

		$mResponse = M::mock('Vinelab\Http\Response');
		$mResponse->shouldReceive('json')->once()->andReturn(null);
		$mResponse->shouldReceive('content')->once()->andReturn("access_token={$returnedAccessToken}&expires={$expires}");

		$this->mHttpClient->shouldReceive('get')->andReturn($mResponse);

		$f = new Facebook($this->mConfig, $this->mHttpClient);

		$requestAccessToken = static::getProtectedMethod('requestAccessToken', $f);
		$accessToken = $requestAccessToken->invokeArgs($f, ['123']);

		$this->assertInstanceOf('Vinelab\Auth\Social\AccessToken', $accessToken);
		$this->assertEquals($returnedAccessToken, $accessToken->token);
		$this->assertEquals($expires, $accessToken->expires);
	}

	public function testParseAccessToekenResponse()
	{
		$f = new Facebook($this->mConfig, $this->mHttpClient);

		$mResponse = M::mock('Vinelab\Http\Response');
		$mResponse->shouldReceive('json')->once()->andReturn(false);
		$mResponse->shouldReceive('content')->once()->andReturn('access_token=123&expires=1234');

		$parseAccessTokenResponse = static::getProtectedMethod('parseAccessTokenResponse', $f);
		$parseAccessTokenResponse->invokeArgs($f, [$mResponse]);
	}

	protected static function getProtectedMethod($name, $class)
	{
		$class = new \ReflectionClass(get_class($class));
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

}