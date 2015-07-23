<?php

// IMPORTANT! Beware remove any of the configuration parameters would break functionality

return [

    'facebook' => [

        // App Configuration

        'api_key' => '285387068232524',

        'secret' => '6bf8ac651d75726605b8bcbea5944591',

        'redirect_uri' => 'http://localhost:8000/auth/social/facebook/callback',

        'permissions' => 'email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_website',

        // Graph Settings - Better not change this stuff unless Facebook updates their API

        'api_url' => 'https://graph.facebook.com',

        'authentication_url' => 'https://www.facebook.com/dialog/oauth/',

        'token_url' => 'https://graph.facebook.com/oauth/access_token',

        'profile_uri' => '/me',

    ],

    'twitter' => [

        'consumer_key' => 'e9fdR2c30HdioJIkJH1cA',

        'consumer_secret' => 'ggou13Sxd2bzWhihqkLhiYLG3OFGp4Svcgbl5Knn2s',

        'access_token' => '158660252-R4Ek0afcsmRRjl8PaE9ReROC7r6vpjcaY1iUTWCo',

        'access_token_secret' => '2wjSH4LPprGDih5CTad9z7sAZBTvK5pN5UEPPFGfg',

        'callback_url' => 'http://localhost:8000/auth/twitter/callback',

        // API Settings - this should not change unless Twitter updates their API

        'version' => 1.1,

        'api_url' => 'https://api.twitter.com',

        'auth_api_url' => 'https://api.twitter.com/oauth',

        'access_token_uri' => '/access_token',

        'authentication_uri' => '/authenticate',

        'authorization_uri' => '/authorize',

        'request_token_uri' => '/request_token',

        'profile_uri' => '/users/show',

        'verify_credentials_uri' => '/account/verify_credentials',

        'user_agent' => 'something about app',

    ],
];
