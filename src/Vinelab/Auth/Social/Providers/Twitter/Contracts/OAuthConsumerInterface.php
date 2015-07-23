<?php

namespace Vinelab\Auth\Social\Providers\Twitter\Contracts;

interface OAuthConsumerInterface
{
    public function make($key, $secret, $redirect_url = null);
}
