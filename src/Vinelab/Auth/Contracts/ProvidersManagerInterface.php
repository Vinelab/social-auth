<?php

namespace Vinelab\Auth\Contracts;

interface ProvidersManagerInterface
{
    public function instantiate($provider);
}
