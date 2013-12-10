<?php namespace Vinelab\Auth\Tests\Social\Providers\Twitter;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use Vinelab\Auth\Social\Providers\Twitter\OAuthToken;

class OAuthTokenTest extends TestCase {

    public function setUp()
    {
        $this->req_tok_sample = file('./tests/samples/twt_request_token.txt')[0];
        $this->acc_tok_sample = file('./tests/samples/twt_access_token.txt')[0];

        $this->token = new OAuthToken;

        $this->response = M::mock('Vinelab\Http\Response');
    }

    public function test_regular_token()
    {
        $key = 'Obla Di Obla Da';
        $secret = 'life-goes-on';

        $token = $this->token->make($key, $secret);

        $this->assertInstanceOf(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface',$token);
        $this->assertInstanceOf('Vinelab\Auth\Social\Providers\Twitter\OAuthToken', $token);

        $this->assertEquals($key, $token->key);
        $this->assertEquals($secret, $token->secret);
        $this->assertNull($token->batata_puree);
    }

    public function test_verifier()
    {
        $key = 'strawberry fields';
        $verifier = 'nothing-is-real';

        $token = $this->token->verifier($key, $verifier);

        $this->assertInstanceOf(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface',$token);
        $this->assertInstanceOf('Vinelab\Auth\Social\Providers\Twitter\OAuthToken', $token);

        $this->assertEquals($key, $token->key);
        $this->assertEquals($verifier, $token->verifier);
        $this->assertNull($token->quoiuuuuaaaaa);
    }

    public function test_making_request_token_from_response()
    {
        $this->response->shouldReceive('content')->once()
            ->andReturn($this->req_tok_sample);

        $token = $this->token->makeRequestToken($this->response);

        $this->assertInstanceOf(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface',$token);
        $this->assertInstanceOf('Vinelab\Auth\Social\Providers\Twitter\OAuthToken', $token);

        parse_str($this->req_tok_sample, $params);

        $this->assertEquals($params['oauth_token'], $token->key);
        $this->assertEquals($params['oauth_token_secret'], $token->secret);
        $this->assertEquals((boolean) $params['oauth_callback_confirmed'], $token->callback_confirmed);

        $this->assertNull($token->dahman_batta);
    }

    /**
     * @depends test_making_request_token_from_response
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_making_null_request_token_response()
    {
        $this->response->shouldReceive('content')->once()
            ->andReturn(null);

        $this->token->makeRequestToken($this->response);
    }

    /**
     * @depends test_making_request_token_from_response
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_making_empty_request_token_response()
    {
        $this->response->shouldReceive('content')->once()
            ->andReturn('');

        $this->token->makeRequestToken($this->response);
    }

    /**
     * @depends test_making_request_token_from_response
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_making_invalid_request_token_response()
    {
        $this->response->shouldReceive('content')->once()
            ->andReturn('87ituykghjasdf9ot7ukygajhsdf9y7ilgubhj,');

        $this->token->makeRequestToken($this->response);
    }

    public function test_making_access_token()
    {
        $this->response->shouldReceive('content')->once()
            ->andReturn($this->acc_tok_sample);

        $token = $this->token->makeAccessToken($this->response);

        $this->assertInstanceOf(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface',$token);
        $this->assertInstanceOf('Vinelab\Auth\Social\Providers\Twitter\OAuthToken', $token);

        parse_str($this->acc_tok_sample, $params);

        $this->assertEquals($params['oauth_token'], $token->key);
        $this->assertEquals($params['oauth_token_secret'], $token->secret);
        $this->assertEquals($params['user_id'], $token->user_id);
        $this->assertEquals($params['screen_name'], $token->screen_name);

        $this->assertNull($token->bijou_sherid);
    }

    /**
     * @depends test_making_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_making_null_access_token()
    {
        $this->response->shouldReceive('content')->once()
            ->andReturn(null);

        $token = $this->token->makeAccessToken($this->response);
    }

    /**
     * @depends test_making_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_making_empty_access_token()
    {
        $this->response->shouldReceive('content')->once()
            ->andReturn('');

        $token = $this->token->makeAccessToken($this->response);
    }

    /**
     * @depends test_making_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_making_invalid_access_token()
    {
        $this->response->shouldReceive('content')->once()
            ->andReturn('this-is-spartaaaaaaaaaaaaaa');

        $token = $this->token->makeAccessToken($this->response);
    }

    public function test_validating_request_token()
    {
        parse_str($this->req_tok_sample, $params);
        $this->assertTrue($this->token->validateRequestTokenResponse($params));
    }

    /**
     * @depends test_validating_request_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_fails_validating_request_token_with_missing_token()
    {
        $this->token->validateRequestTokenResponse(['oauth_token_secret'=>'something']);
    }

    /**
     * @depends test_validating_request_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_fails_validating_request_token_with_empty_token()
    {
        $this->token->validateRequestTokenResponse([
            'oauth_token' => '',
            'oauth_token_secret'=>'something'
        ]);
    }

    /**
     * @depends test_validating_request_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_fails_validating_request_token_with_null_token()
    {
        $this->token->validateRequestTokenResponse([
            'oauth_token' => null,
            'oauth_token_secret'=>'something'
        ]);
    }

    /**
     * @depends test_validating_request_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_fails_validating_request_token_with_missing_secret()
    {
        $this->token->validateRequestTokenResponse(['oauth_token'=>'something']);
    }

    /**
     * @depends test_validating_request_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_fails_validating_request_token_with_empty_secret()
    {
        $this->token->validateRequestTokenResponse([
            'oauth_token' => 'something',
            'oauth_token_secret'=>''
        ]);
    }

    /**
     * @depends test_validating_request_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage request token
     */
    public function test_fails_validating_request_token_with_null_secret()
    {
        $this->token->validateRequestTokenResponse([
            'oauth_token' => 'tfouuuu',
            'oauth_token_secret'=> null
        ]);
    }

    public function test_validating_access_token()
    {
        parse_str($this->acc_tok_sample, $params);
        $this->assertTrue($this->token->validateAccessTokenResponse($params));
    }

    /**
     * @depends test_validating_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_fails_validating_access_token_with_missing_token()
    {
        $this->token->validateAccessTokenResponse(['oauth_token_secret'=>'something']);
    }

    /**
     * @depends test_validating_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_fails_validating_access_token_with_empty_token()
    {
        $this->token->validateAccessTokenResponse([
            'oauth_token' => '',
            'oauth_token_secret'=>'some-secret'
        ]);
    }

    /**
     * @depends test_validating_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_fails_validating_access_token_with_null_token()
    {
        $this->token->validateAccessTokenResponse([
            'oauth_token' => null,
            'oauth_token_secret'=>'some-secret'
        ]);
    }

    /**
     * @depends test_validating_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_fails_validating_access_token_with_missing_secret()
    {
        $this->token->validateAccessTokenResponse(['oauth_token'=>'something']);
    }

    /**
     * @depends test_validating_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_fails_validating_access_token_with_empty_secret()
    {
        $this->token->validateAccessTokenResponse([
            'oauth_token' => 'some-token',
            'oauth_token_secret'=>''
        ]);
    }

    /**
     * @depends test_validating_access_token
     * @expectedException Vinelab\Auth\Exceptions\InvalidOAuthTokenException
     * @expectedExceptionMessage access token
     */
    public function test_fails_validating_access_token_with_null_secret()
    {
        $this->token->validateAccessTokenResponse([
            'oauth_token' => 'some-token',
            'oauth_token_secret'=> null
        ]);
    }
}