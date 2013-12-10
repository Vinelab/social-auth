<?php namespace Vinelab\Auth\Tests\Social\Providers;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use Vinelab\Auth\Social\Providers\Facebook;

class FacebookTest extends TestCase {

    protected static $fb_profile;

    public static function setUpBeforeClass()
    {
        $profile_file = file('./tests/samples/fb_profile.json');
        static::$fb_profile = json_decode(array_pop($profile_file));
    }

    public function setUp()
    {
        $this->config       = M::mock('Illuminate\Config\Repository');
        $this->redirect     = M::mock('Illuminate\Routing\Redirector');
        $this->http         = M::mock('Vinelab\Http\Client');
        $this->store        = M::mock('Vinelab\Auth\Contracts\StoreInterface');
        $this->profile      = M::mock('Vinelab\Auth\Contracts\ProfileInterface');
        $this->access_token = M::mock(
            'Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface');
        $this->response     = M::mock('Vinelab\Http\Response');

         $this->fb_settings = [
            'api_key'            => 'api_key',
            'secret'             => 'secret',
            'redirect_uri'       => 'redirect_uri',
            'permissions'        => 'permissions',
            'api_url'            => 'api_url',
            'authentication_url' => 'authentication_url',
            'token_url'          => 'token_url',
            'profile_uri'        => 'profile_uri'
        ];

        $this->config->shouldReceive('get')->once()
            ->with('social.facebook')
            ->andReturn($this->fb_settings);
    }

    public function test_generating_state()
    {
        $fb = $this->fb();
        $makeState = static::getProtectedMethod('makeState', $fb);

        $state = $makeState->invokeArgs($fb, []);

        $this->assertNotNull($state);
        $this->assertEquals('string', gettype($state));
    }

    public function test_generating_auth_url()
    {
        $fb = $this->fb();
        $this->assertEquals(
            'authentication_url?client_id=api_key&redirect_uri=redirect_uri&scope=permissions&state=my-state',
            $fb->authURL('my-state'));
    }

    /**
     * @depends test_generating_state
     * @depends test_generating_auth_url
     */
    public function test_authentication()
    {
        $this->store->shouldReceive('put')->once();
        $this->redirect->shouldReceive('to')->once();

        $fb = $this->fb();
        $fb->authenticate();
    }

    public function test_requesting_access_token()
    {
        $code = "fb's code";

        $this->http->shouldReceive('get')
            ->with([
                'url' => 'token_url',
                'params' => [
                    'client_id'     => 'api_key',
                    'redirect_uri'  => 'redirect_uri',
                    'client_secret' => 'secret',
                    'code'          => $code,
                    'format'        => 'json'
                ]
            ])->once()
            ->andReturn($this->response);

        $this->access_token->shouldReceive('make')->once()->andReturn($this->access_token);

        $fb = $this->fb();

        $token = $fb->requestAccessToken($code);
        $this->assertInstanceOf('Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface', $token);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\InvalidFacebookCodeException
     */
    public function test_requesting_access_token_with_empty_code()
    {
        $code = '';

        $this->http->shouldReceive('get')
            ->with([
                'url' => 'token_url',
                'params' => [
                    'client_id'     => 'api_key',
                    'redirect_uri'  => 'redirect_uri',
                    'client_secret' => 'secret',
                    'code'          => $code,
                    'format'        => 'json'
                ]
            ])->once()
            ->andReturn($this->response);

        $this->access_token->shouldReceive('make')->once()->andReturn($this->access_token);

        $fb = $this->fb();

        $token = $fb->requestAccessToken($code);
        $this->assertInstanceOf('Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface', $token);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\InvalidFacebookCodeException
     */
    public function test_requesting_access_token_with_null_code()
    {
        $code = null;

        $this->http->shouldReceive('get')
            ->with([
                'url' => 'token_url',
                'params' => [
                    'client_id'     => 'api_key',
                    'redirect_uri'  => 'redirect_uri',
                    'client_secret' => 'secret',
                    'code'          => $code,
                    'format'        => 'json'
                ]
            ])->once()
            ->andReturn($this->response);

        $this->access_token->shouldReceive('make')->once()->andReturn($this->access_token);

        $fb = $this->fb();

        $token = $fb->requestAccessToken($code);
        $this->assertInstanceOf('Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface', $token);
    }

    public function test_parsing_profile()
    {
        $this->response->shouldReceive('json')->once()
            ->andReturn(static::$fb_profile);
        $this->access_token->shouldReceive('token')->once()
            ->andReturn('somAksestochken');

        $this->profile->shouldReceive('instantiate')->once()
            ->with(static::$fb_profile, 'facebook')
            ->andReturn($this->profile);

        $fb = $this->fb();

        $parsed = $fb->parseProfileResponse($this->response, $this->access_token);
        $this->assertInstanceOf('Vinelab\Auth\Contracts\ProfileInterface', $parsed);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\InvalidProfileException
     */
    public function test_parsing_null_profile()
    {
        $this->response->shouldReceive('json')->once()
            ->andReturn(null);

        $fb = $this->fb();

        $fb->parseProfileResponse($this->response, $this->access_token);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\InvalidProfileException
     */
    public function test_parsing_empty_profile()
    {
        $this->response->shouldReceive('json')->once()
            ->andReturn('');

        $fb = $this->fb();

        $fb->parseProfileResponse($this->response, $this->access_token);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\InvalidProfileException
     * @expectedExceptionCode 508
     * @expectedExceptoionMessage err-type: err-msg
     */
    public function test_parsing_erroneous_profile()
    {
        $p = new \stdClass;
        $p->error = new \stdClass;
        $p->error->type = 'err-type';
        $p->error->message = 'err-msg';
        $p->error->code = 508;

        $this->response->shouldReceive('json')->once()
            ->andReturn($p);

        $fb = $this->fb();

        $fb->parseProfileResponse($this->response, $this->access_token);
    }

    /**
     * @depends test_parsing_profile
     */
    public function test_requesting_profile()
    {
        $this->access_token->shouldReceive('token')->once()
            ->andReturn('tochkan');

        $this->response->shouldReceive('json')->once()
            ->andReturn(static::$fb_profile);
        $this->access_token->shouldReceive('token')->once()
            ->andReturn('tochkan');

        $this->profile->shouldReceive('instantiate')->once()
            ->with(static::$fb_profile, 'facebook')
            ->andReturn($this->profile);

        $this->http->shouldReceive('get')->once()
            ->with([
                'url' => 'api_urlprofile_uri',
                'params' => ['access_token'=>'tochkan']
            ])->andReturn($this->response);

        $fb = $this->fb();
        $fb->requestProfile($this->access_token);
    }

    /**
     * @depends test_requesting_access_token
     * @depends test_requesting_profile
     */
    public function test_successful_callback()
    {
        $code = 'some-doodled-fb-mothafuckin-code';
        $state_cached = true;

        $this->prepareCallbackTest($code, $state_cached);

        $fb = $this->fb();
        $fb->callback(['code' => $code, 'state'=>'some-state']);
    }

    public function test_authentication_with_token()
    {
        $this->access_token->shouldReceive('makeFromToken')
            ->with('tochkan')
            ->andReturn($this->access_token);

        $this->access_token->shouldReceive('token')
            ->andReturn('tochkan');

        $this->access_token->shouldReceive('token')->once()
            ->andReturn('tochkan');

        $this->response->shouldReceive('json')->once()
            ->andReturn(static::$fb_profile);
        $this->access_token->shouldReceive('token')->once()
            ->andReturn('tochkan');

        $this->profile->shouldReceive('instantiate')->once()
            ->with(static::$fb_profile, 'facebook')
            ->andReturn($this->profile);

        $this->http->shouldReceive('get')->once()
            ->with([
                'url' => 'api_urlprofile_uri',
                'params' => ['access_token'=>'tochkan']
            ])->andReturn($this->response);

        $fb = $this->fb();
        $fb->authenticateWithToken('tochkan');
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\AuthenticationException
     */
    public function test_callback_error_handling()
    {
        $fb = $this->fb();
        $fb->callback(['error' => 'woops', 'error_description' => 'something went wrong']);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\AuthenticationException
     * @expectedException invalid state invalid code
     */
    public function test_callback_missing_code()
    {
        $fb = $this->fb();
        $fb->callback(['state'=>'some-state']);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\AuthenticationException
     * @expectedException invalid state
     */
    public function test_callback_missing_state()
    {
        $fb = $this->fb();
        $fb->callback(['code'=>'some-code']);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\AuthenticationException
     * @expectedException state expired
     */
    public function test_callback_invalid_state()
    {
        $this->store->shouldReceive('has')->once()
            ->andReturn(false);

        $fb = $this->fb();
        $fb->callback(['code'=>'some-code', 'state'=>'some-state']);
    }

    protected function prepareCallbackTest($code, $cached)
    {
        $this->store->shouldReceive('has')->once()
            ->andReturn($cached);
        $this->access_token->shouldReceive('make')->once()
            ->andReturn($this->access_token);
        $this->access_token->shouldReceive('token')->once()
            ->andReturn('tochkan');

        $this->http->shouldReceive('get')->once()
            ->with([
                'url' => 'api_urlprofile_uri',
                'params' => ['access_token'=>'tochkan']
            ])->andReturn($this->response);

        $this->http->shouldReceive('get')
            ->with([
                'url' => 'token_url',
                'params' => [
                    'client_id'     => 'api_key',
                    'redirect_uri'  => 'redirect_uri',
                    'client_secret' => 'secret',
                    'code'          => $code,
                    'format'        => 'json'
                ]
            ])->once()
            ->andReturn($this->response);

        $this->response->shouldReceive('json')->once()
            ->andReturn(static::$fb_profile);

        $this->profile->shouldReceive('instantiate')->once()
            ->with(static::$fb_profile, 'facebook')
            ->andReturn($this->profile);
    }

    protected function fb()
    {
        return new Facebook($this->config,
                            $this->redirect,
                            $this->http,
                            $this->store,
                            $this->profile,
                            $this->access_token);
    }

    /**
     * @todo Remove this into a Vinelab specific test
     * which gets inherited by all test calasses
     * to have this capability. Cause this has been repeated
     * many times.
     */
    protected static function getProtectedMethod($name, $class)
    {
        $class = new \ReflectionClass(get_class($class));
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}