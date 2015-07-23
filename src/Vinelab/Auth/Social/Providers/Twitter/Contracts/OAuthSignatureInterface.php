<?php

namespace Vinelab\Auth\Social\Providers\Twitter\Contracts;

/*
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */

interface OAuthSignatureInterface
{
    public function method();

    public function get(OAuthConsumerInterface $consumer,
                        OAuthTokenInterface $token,
                        $httpverb,
                        $url,
                        $params = []);
}
