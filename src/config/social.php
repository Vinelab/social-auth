<?php

// IMPORTANT! Beware remove any of the configuration parameters would break functionality

return [

	'facebook' => [

		// App Configuration

		'api_key'      => '285387068232524',

		'secret'       => '6bf8ac651d75726605b8bcbea5944591',

		'redirect_uri' => 'http://localhost:8000/auth/social/facebook/callback',

		'permissions'  => 'email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_website',

		// Graph Settings - Better not change this stuff unless Facebook updates their API

		'api_url'            => 'https://graph.facebook.com',

		'authentication_url' => 'https://www.facebook.com/dialog/oauth/',

		'token_url'          => 'https://graph.facebook.com/oauth/access_token',

		'profile_uri'        => '/me'

	]
];