<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use Vinelab\Auth\Social\Networks\Twitter as Twitter;

class TwitterTest extends TestCase {

    public function setUp()
    {
        $this->samplesDir = './tests/samples';

        $this->settings = [
            'base_url'     => 'http://localhost/twitter',
            'key'          => 'somekey-twitter',
            'secret'       => 'some-secret-key-twitter',
            'redirect_uri' => 'http://localhost/twitter/callback'
        ];

        $this->mConfig = M::mock('Illuminate\Config\Repository');
        $this->mConfig->shouldReceive('get')
            ->once('auth::social.twitter')
            ->andReturn($this->settings);

        $this->mResponse = M::mock('Vinelab\Http\Response');
        $this->mHttpClient = M::mock('Vinelab\Http\Client[get]');
    }

    public function testAuthentication()
    {
        $t = new Twitter($this->mConfig, $this->mHttpClient);
        $this->assertTrue(method_exists($t, 'authenticate'));
        $this->assertTrue(method_exists($t, 'authenticationCallback'));
    }

}