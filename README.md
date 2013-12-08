[![Build Status](https://travis-ci.org/Vinelab/social-auth.png?branch=3.0.0-dev)](https://travis-ci.org/Vinelab/social-auth)
# Social Authentication - Laravel 4

## Installation
Using [composer](http://getcomposer.org) require the package [vinelab/social-auth](https://packagist.org/packages/vinelab/social-auth).
Edit **app.php** and add ```'Vinelab\Auth\AuthServiceProvider'``` to the ```'providers'``` array.
It will automatically alias itself as SocialAuth which is a Facade.

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