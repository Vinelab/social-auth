<?php

namespace Vinelab\Auth\Tests\Social\Providers;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;
use Vinelab\Auth\Social\Providers\Twitter;

class TwitterTest extends TestCase
{
    public function setUp()
    {
        $this->config = M::mock('Illuminate\Config\Repository');
        $this->http = M::mock('Vinelab\Http\Client');
        $this->redirect = M::mock('Illuminate\Routing\Redirector');
        $this->store = M::mock('Vinelab\Auth\Contracts\StoreInterface');
        $this->profile = M::mock('Vinelab\Auth\Contracts\ProfileInterface');
        $this->signature = M::mock(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface');
        $this->consumer = M::mock(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface');
        $this->token = M::mock(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface');
        $this->oauth = M::mock(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthInterface');

        $this->response = M::mock('Vinelab\Http\Response');

        $this->settings = [
            'version' => '1.1',
            'consumer_key' => 'conskey',
            'consumer_secret' => 'universe',
            'api_url' => 'api://url',
            'auth_api_url' => 'auth://api.url',
            'authentication_uri' => '/authenticate',
            'verify_credentials_uri' => '/verify_credentials',
        ];

        $this->config->shouldReceive('get')->once()
            ->with('social.twitter')
            ->andReturn($this->settings);

        $this->consumer->shouldReceive('make')->once()
            ->with($this->settings['consumer_key'], $this->settings['consumer_secret']);

        $this->twt = new Twitter($this->config,
                                $this->http,
                                $this->redirect,
                                $this->store,
                                $this->profile,
                                $this->signature,
                                $this->consumer,
                                $this->token,
                                $this->oauth);
    }

    public function test_instantiation()
    {
        $this->assertInstanceOf(
            'Vinelab\Auth\Social\Providers\Twitter', $this->twt);
    }

    public function test_authentication()
    {
        $this->oauth->shouldReceive('getRequestToken')->once()
            ->with($this->settings, $this->consumer, $this->token)
            ->andReturn($this->token);

        $this->token->key = 'request-token-retrieved-from-twittaaaaa3333';

        $auth_url = $this->settings['auth_api_url'].$this->settings['authentication_uri'];
        $auth_url .= '?'.http_build_query(['oauth_token' => $this->token->key]);

        $this->redirect->shouldReceive('to')->once()
            ->with($auth_url)
            ->andReturn($this->response);

        $this->assertInstanceOf('Vinelab\Http\Response', $this->twt->authenticate());
    }

    public function test_generating_api_url()
    {
        $url = $this->twt->api('/somewhere');
        $expected = $this->settings['api_url'].'/'.
                    $this->settings['version'].
                    '/somewhere.json';

        $this->assertEquals($expected, $url);
    }

    public function test_getting_profile()
    {
        $headers = ['here'];
        $url = $this->settings['api_url'].'/'.
                $this->settings['version'].
                $this->settings['verify_credentials_uri'].'.json';

        $this->oauth->shouldReceive('headers')->once()
            ->with($this->settings, 'GET', $url, $this->consumer, $this->token)
            ->andReturn($headers);

        // make sure it adds the content-type
        array_push($headers, 'Content-Type: application/x-www-form-urlencoded');

        $response = M::mock('Vinelab\Http\Response');
        $response->shouldReceive('statusCode')->once()
            ->andReturn(200);
        $response->shouldReceive('json')->once()
            ->andReturn(M::mock('stdClass'));

        $this->profile->shouldReceive('instantiate')->once()
            ->with(M::type('object'), 'twitter');

        $this->http->shouldReceive('get')->once()
            ->with(['url' => $url, 'headers' => $headers])
            ->andReturn($response);

        $this->twt->getProfile($this->token);
    }

    public function test_authentication_with_token()
    {
        $token = 'my-token-to-be-alive';

        $this->token->shouldReceive('make')->once()
            ->with($token, null)
            ->andReturn($this->token);

        // brought in from the getProfile test
        $headers = ['here'];
        $url = $this->settings['api_url'].'/'.
                $this->settings['version'].
                $this->settings['verify_credentials_uri'].'.json';

        $this->oauth->shouldReceive('headers')->once()
            ->with($this->settings, 'GET', $url, $this->consumer, $this->token)
            ->andReturn($headers);

        // make sure it adds the content-type
        array_push($headers, 'Content-Type: application/x-www-form-urlencoded');

        $response = M::mock('Vinelab\Http\Response');
        $response->shouldReceive('statusCode')->once()
            ->andReturn(200);
        $response->shouldReceive('json')->once()
            ->andReturn(M::mock('stdClass'));

        $this->profile->shouldReceive('instantiate')->once()
            ->with(M::type('object'), 'twitter');

        $this->http->shouldReceive('get')->once()
            ->with(['url' => $url, 'headers' => $headers])
            ->andReturn($response);

        // -- till here

        $this->twt->authenticateWithToken($token);
    }

    /**
     * @depends test_getting_profile
     */
    public function test_callback()
    {
        $token = 'my-token-so-potent';
        $verifier = 'my-verifier-sucks';

        $this->token->shouldReceive('verifier')->once()
            ->with($token, $verifier)
            ->andReturn($this->token);

        $this->oauth->shouldReceive('getAccessToken')->once()
            ->with($this->settings, $this->consumer, $this->token)
            ->andReturn($this->token);

        // brought in from the getProfile test
        $headers = ['here'];
        $url = $this->settings['api_url'].'/'.
                $this->settings['version'].
                $this->settings['verify_credentials_uri'].'.json';

        $this->oauth->shouldReceive('headers')->once()
            ->with($this->settings, 'GET', $url, $this->consumer, $this->token)
            ->andReturn($headers);

        // make sure it adds the content-type
        array_push($headers, 'Content-Type: application/x-www-form-urlencoded');

        $response = M::mock('Vinelab\Http\Response');
        $response->shouldReceive('statusCode')->once()
            ->andReturn(200);
        $response->shouldReceive('json')->once()
            ->andReturn(M::mock('stdClass'));

        $this->profile->shouldReceive('instantiate')->once()
            ->with(M::type('object'), 'twitter');

        $this->http->shouldReceive('get')->once()
            ->with(['url' => $url, 'headers' => $headers])
            ->andReturn($response);
        // -- till here

        $this->twt->callback([
            'oauth_token' => $token,
            'oauth_verifier' => $verifier,
        ]);
    }

    /**
     * @depends test_callback
     * @expectedException Vinelab\Auth\Exceptions\AuthenticationCanceledException
     */
    public function test_callback_denied()
    {
        $this->twt->callback(['denied' => 'order like shit']);
    }

    /**
     * @depends test_callback
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     */
    public function test_callback_with_no_oauth_token()
    {
        $this->twt->callback(['oauth_verifier' => 'take it in']);
    }

    /**
     * @depends test_callback
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     */
    public function test_callback_with_no_oauth_token_verifier()
    {
        $this->twt->callback(['oauth_token' => 'take it in']);
    }
}
