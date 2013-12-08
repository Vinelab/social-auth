<?php namespace Vinelab\Auth\Tests\Social;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use  Vinelab\Auth\Social\AccessToken;

class AccessTokenTest extends TestCase {

    public function setUp()
    {
        // a sample of the returned access token from facebook
        $this->at_sample = 'access_token=CAAEDjtrTU0wBAEkWVZB7yDYoDyw7jFZANNuc667ZBkcXdXYZCybHgTz2PtCawKSTVcqP3C14y5dVWwpbY0VWaXe9rpalnvSCkdiyWbIq7Jz52hiWKXZBuIRLsgV7NMo9cb4kyxXPqMAQkcmRwKXTSigbTAwzBLQKtfPVszdLj7bYwMak7U3vb&expires=5183481';
        $this->at_error_sample = '{"error": {"message": "Error validating access token: The session is invalid because the user logged out.", "type": "OAuthException", "code": 190}}';

        $this->response = M::mock('Vinelab\Http\Response');

        $this->access_token = new AccessToken;
    }

    public function test_instantiation()
    {
        $this->response->shouldReceive('json')->once()
            ->andReturn(json_decode($this->at_sample));

        $this->response->shouldReceive('content')->once()
            ->andReturn($this->at_sample);

        parse_str($this->at_sample, $sample);

        $access_token = $this->access_token->make($this->response);
        $this->assertInstanceOf('Vinelab\Auth\Social\AccessToken', $access_token);
        $this->assertEquals($sample['access_token'], $access_token->token());
        $this->assertEquals($sample['expires'], $access_token->expiry());
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\AccessTokenException
     * @expectedExceptionMessage OAuthException: Error validating access token: The session is invalid because the user logged out.
     * @expectedExceptionCode 190
     */
    public function test_handling_errors()
    {
        $this->response->shouldReceive('json')->once()
            ->andReturn(json_decode($this->at_error_sample));

        $this->access_token->make($this->response);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\AccessTokenException
     * @expectedExceptionMessage no access token received
     */
    public function test_null_response()
    {
        $this->response->shouldReceive('json')->once()
            ->andReturn(null);
        $this->response->shouldReceive('content')->once()
            ->andReturn(null);

        $this->access_token->make($this->response);
    }

   /**
     * @expectedException Vinelab\Auth\Exceptions\AccessTokenException
     * @expectedExceptionMessage no access token received
     */
    public function test_empty_response()
    {
        $this->response->shouldReceive('json')->once()
            ->andReturn('');
        $this->response->shouldReceive('content')->once()
            ->andReturn('');

        $this->access_token->make($this->response);
    }

    /**
     * @expectedException Vinelab\Auth\Exceptions\AccessTokenException
     * @expectedExceptionMessage no access token received
     */
    public function test_different_response()
    {
        $this->response->shouldReceive('json')->once()
            ->andReturn('something else');
        $this->response->shouldReceive('content')->once()
            ->andReturn('another thing here');

        $this->access_token->make($this->response);
    }
}