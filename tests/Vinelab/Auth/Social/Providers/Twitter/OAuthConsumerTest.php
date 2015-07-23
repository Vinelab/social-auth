<?php

namespace Vinelab\Auth\Tests\Social\Providers\Twitter;

use PHPUnit_Framework_TestCase as TestCase;
use Vinelab\Auth\Social\Providers\Twitter\OAuthConsumer;

class OAuthConsumerTest extends TestCase
{
    public function test_consumer()
    {
        $key = 'some-key';
        $secret = 'ooooohhhh--it is a secret';
        $redirect_url = 'parallel://universe';

        $consumer = (new OAuthConsumer())->make($key, $secret, $redirect_url);

        $this->assertInstanceOf(
            'Vinelab\Auth\Social\Providers\Twitter\OAuthConsumer',
            $consumer);

        $this->assertEquals($key, $consumer->key);
        $this->assertEquals($secret, $consumer->secret);
        $this->assertEquals($redirect_url, $consumer->redirect_url);
        $this->assertNull($consumer->shi_shaghle_mish_mawjoudeh);
    }
}
