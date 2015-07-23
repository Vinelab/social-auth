<?php

namespace Vinelab\Auth\Contracts;

interface ProfileInterface
{
    public function instantiate($profile, $provider);

    public function info();

    public function provider();
}
