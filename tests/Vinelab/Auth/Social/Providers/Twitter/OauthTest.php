<?php

namespace Vinelab\Auth\Tests\Social\Providers\Twitter;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;
use Vinelab\Auth\Social\Providers\Twitter\OAuth;

class OauthTest extends TestCase
{
    public function setUp()
    {
        $this->consumer = M::mock(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface');
        $this->signature = M::mock(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface');

        $this->token = M::mock(
            'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface');
        $this->http = M::mock('Vinelab\Http\Client');

        $this->settings = ['user_agent' => 'inspector gadget'];

        $this->consumer->key = 'Yale';
        $this->consumer->secret = 'come-tell-me-secret';

        $this->oauth = new OAuth($this->token, $this->signature, $this->http);
    }

    public function test_normalizing_headers()
    {
        $params = ['param1' => 'v1', 'param2' => 'v2', 'param3' => 'v3'];
        $normalized = $this->oauth->normalizeHeaders($params);

        foreach (explode(',', $normalized) as $h) {
            $v = explode('=', $h);

            $this->assertTrue(array_key_exists($v[0], $params));
            $this->assertEquals('"'.$params[$v[0]].'"', $v[1]);
        }
    }

    public function test_generating_unique_nonce()
    {
        $nonces = [];

        for ($i = 0; $i < 10; ++$i) {
            $nonce = $this->oauth->generateNonce();
            $this->assertFalse(in_array($nonce, $nonces));
            array_push($nonces, $nonce);
        }

        // fork a process to run in parallel
        // to see whether running in a multi-process
        // environment might break it
        $pid = pcntl_fork();

        if ($pid) {
            // parent process runs what is here
            for ($i = 0; $i < 10; ++$i) {
                $nonce = $this->oauth->generateNonce();
                $this->assertFalse(in_array($nonce, $nonces));
                array_push($nonces, $nonce);
            }

            // protect agains Zombie children
            pcntl_waitpid($pid, $status);
        } else {
            // child process runs what is here
            for ($i = 0; $i < 10; ++$i) {
                $nonce = $this->oauth->generateNonce();
                $this->assertFalse(in_array($nonce, $nonces));
                array_push($nonces, $nonce);
            }
            exit(1);
        }
    }

    public function test_generating_timestamps()
    {
        $this->assertEquals('integer', gettype($this->oauth->generateTimestamp()));
    }

    public function test_generating_headers_with_null_token()
    {
        $this->token->key = null;
        $this->token->secret = null;

        $this->signature->shouldReceive('method')->once()
            ->andReturn('HMAC-SHA1');
        $this->signature->shouldReceive('get')->once()
            ->andReturn('some-god-damn-head-drifting-feet-burning-signature');

        $headers = $this->oauth->headers($this->settings,
                                        'GET',
                                        'some://url.net',
                                        $this->consumer,
                                        $this->token);

        $oauth_headers = $this->getAuthorizationHeaders($headers);

        $this->assertArrayHasKey('oauth_consumer_key', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_consumer_key']);

        $this->assertArrayHasKey('oauth_nonce', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_nonce']);

        $this->assertArrayHasKey('oauth_signature_method', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_signature_method']);

        $this->assertArrayHasKey('oauth_timestamp', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_timestamp']);

        $this->assertArrayHasKey('oauth_version', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_version']);

        $this->assertArrayHasKey('oauth_signature', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_signature']);

        $original_oauth_headers = $oauth_headers;
        uksort($oauth_headers, 'strcmp');

        $this->assertTrue(
            $this->arraysHaveSimilarIndexes($original_oauth_headers, $oauth_headers)
        );
    }

    public function test_generating_headers_with_token()
    {
        $this->token->key = 'flesh-and-the-power-it-holds';
        $this->token->secret = 'death';

        $this->signature->shouldReceive('method')->once()
            ->andReturn('HMAC-SHA1');
        $this->signature->shouldReceive('get')->once()
            ->andReturn('some-god-damn-head-drifting-feet-burning-signature');

        $headers = $this->oauth->headers($this->settings,
                                        'GET',
                                        'some://url.net',
                                        $this->consumer,
                                        $this->token);

        $oauth_headers = $this->getAuthorizationHeaders($headers);

        $this->assertArrayHasKey('oauth_consumer_key', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_consumer_key']);

        $this->assertArrayHasKey('oauth_nonce', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_nonce']);

        $this->assertArrayHasKey('oauth_signature_method', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_signature_method']);

        $this->assertArrayHasKey('oauth_timestamp', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_timestamp']);

        $this->assertArrayHasKey('oauth_version', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_version']);

        $this->assertArrayHasKey('oauth_signature', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_signature']);

        $this->assertArrayHasKey('oauth_token', $oauth_headers);
        $this->assertNotEmpty($oauth_headers['oauth_token']);

        $original_oauth_headers = $oauth_headers;

        uksort($oauth_headers, 'strcmp');

        $this->assertTrue(
            $this->arraysHaveSimilarIndexes($original_oauth_headers, $oauth_headers)
        );
    }

    public function test_getting_request_token()
    {
        $this->settings['auth_api_url'] = 'auth://api.url';
        $this->settings['request_token_uri'] = '/request_token_uri';
        $this->settings['callback_url'] = 'come://back.here';

        $this->token->key = null;

        $this->signature->shouldReceive('method')->once()
            ->andReturn('HMAC-SHA1');

        $this->signature->shouldReceive('get')->once()
            ->andReturn('some-god-damn-head-drifting-feet-burning-signature');

        $this->http->shouldReceive('post')->once()
            ->with(M::type('array'))
            ->andReturn(M::mock('Vinelab\Http\Response'));

        $this->token->shouldReceive('makeRequestToken')->once()
            ->with(M::type('Vinelab\Http\Response'))
            ->once()
            ->andReturn();

        $this->oauth->getRequestToken($this->settings, $this->consumer, $this->token);
    }

    public function test_getting_access_token()
    {
        $this->settings['auth_api_url'] = 'auth://api.url';
        $this->settings['access_token_uri'] = '/access_token_uri';
        $this->settings['callback_url'] = 'come://back.here';

        $this->token->key = 'space';
        $this->token->verifier = 'cake';

        $this->signature->shouldReceive('method')->once()
            ->andReturn('HMAC-SHA1');

        $this->signature->shouldReceive('get')->once()
            ->andReturn('some-god-damn-head-drifting-feet-burning-signature');

        $this->http->shouldReceive('post')->once()
            ->with(M::type('array'))
            ->andReturn(M::mock('Vinelab\Http\Response'));

        $this->token->shouldReceive('makeAccessToken')->once()
            ->with(M::type('Vinelab\Http\Response'))
            ->once()
            ->andReturn();

        $this->oauth->getAccessToken($this->settings, $this->consumer, $this->token);
    }

    protected function getAuthorizationHeaders($headers)
    {
        $oauth_headers = [];

        foreach (explode(',', $headers[0]) as $header_string) {
            $header_string = trim(str_replace('Authorization: OAuth ', '', $header_string));
            $header = explode('=', $header_string);
            $oauth_headers[$header[0]] = $header[1];
        }

        return $oauth_headers;
    }

    protected function arraysHaveSimilarIndexes($left, $right)
    {
        if (count(array_diff_assoc($left, $right))) {
            return false;
        }

        foreach ($left as $k => $v) {
            if ($v !== $right[$k]) {
                return false;
            }
        }

        return true;
    }
}
