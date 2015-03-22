[![Build Status](https://travis-ci.org/Vinelab/social-auth.png?branch=3.0.0-dev)](https://travis-ci.org/Vinelab/social-auth)
# Social Authentication - Laravel

[![Dependency Status](https://www.versioneye.com//user/projects/53efc9c413bb062f5f0004bb/badge.svg?style=flat)](https://www.versioneye.com//user/projects/53efc9c413bb062f5f0004bb)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/66a62d07-c599-422e-a49a-1900d8f06430/big.png)](https://insight.sensiolabs.com/projects/66a62d07-c599-422e-a49a-1900d8f06430)

## Installation
Using [composer](http://getcomposer.org) require the package [vinelab/social-auth](https://packagist.org/packages/vinelab/social-auth).
Edit **app.php** and add ```'Vinelab\Auth\AuthServiceProvider'``` to the ```'providers'``` array.
It will automatically alias itself as SocialAuth which is a Facade.

## Configuration
Publish the configuration file using `php artisan vendor:publish`

## Usage
```php
<?php

// start the authentication process
$provider = 'facebook';

// inital authentication route
SocialAuth::authenticate($provider);

// callback route should do this
$profile = SocialAuth::profile($provider, Input::get());

$profile->provider(); // facebook
$profile->info(); // the facebook profile information
```
