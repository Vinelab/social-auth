<?php

namespace Vinelab\Auth\Social\Providers\Twitter\Contracts;

interface OAuthInterface
{
    public function getRequestToken($settings,
                                    OAuthConsumerInterface $consumer,
                                    OAuthTokenInterface $token);

    public function getAccessToken($settings,
                                    OAuthConsumerInterface $consumer,
                                    OAuthTokenInterface $token);
}
