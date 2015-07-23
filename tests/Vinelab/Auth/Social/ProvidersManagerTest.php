<?php

namespace Vinelab\Auth\Tests\Social;

use Mockery as M;
use PHPUnit_Framework_TestCase as TestCase;
use Vinelab\Auth\Social\ProvidersManager;

class ProvidersManagerTest extends TestCase
{
    public function setUp()
    {
        $this->config = M::mock('Illuminate\Config\Repository');
        $this->redirector = M::mock('Illuminate\Routing\Redirector');
        $this->http = M::mock('Vinelab\Http\Client');
        $this->store = M::mock('Vinelab\Auth\Contracts\StoreInterface');
        $this->profile = M::mock('Vinelab\Auth\Contracts\ProfileInterface');
        $this->access_token = M::mock('Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface');
        $this->oauth = M::mock('Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthInterface');
        $this->token = M::mock('Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface');
        $this->signature = M::mock('Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface');
        $this->consumer = M::mock('Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface');

        $this->providers = new ProvidersManager($this->config,
                                                $this->redirector,
                                                $this->http,
                                                $this->store,
                                                $this->profile,
                                                $this->access_token,
                                                $this->oauth,
                                                $this->token,
                                                $this->signature,
                                                $this->consumer);
    }

    public function test_supported()
    {
        $this->assertTrue($this->providers->supported('facebook'));
        $this->assertTrue($this->providers->supported('twitter'));
        $this->assertFalse($this->providers->supported('something'));
    }

    public function test_instantiation()
    {
        $this->config->shouldReceive('get')->once()
            ->with('social.facebook')
            ->andReturn(['setting' => 'value']);

        $this->assertInstanceOf(
            'Vinelab\Auth\Social\Providers\Facebook',
            $this->providers->instantiate('facebook'));
    }
}
