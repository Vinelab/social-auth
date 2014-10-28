<?php namespace Vinelab\Auth\Social\Providers\Twitter\Contracts;

use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface;

interface OAuthInterface {

    public function getRequestToken($settings,
                                    OAuthConsumerInterface $consumer,
                                    OAuthTokenInterface $token);

    public function getAccessToken($settings,
                                    OAuthConsumerInterface $consumer,
                                    OAuthTokenInterface $token);
}
