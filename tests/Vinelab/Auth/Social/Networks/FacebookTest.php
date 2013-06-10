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

}