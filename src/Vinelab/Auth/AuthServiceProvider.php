<?php namespace Vinelab\Auth;

use Illuminate\Support\ServiceProvider;

use Vinelab\Auth\Auth;
use Vinelab\Auth\Cache\Store;
use Vinelab\Auth\Social\ProvidersManager;
use Illuminate\Foundation\Application as App;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		$this->publishes([
		    __DIR__.'/../../config/social.php' => config_path('social.php'),
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->register('Vinelab\Http\HttpServiceProvider');
		$this->app->register('Vinelab\Assistant\AssistantServiceProvider');

		$this->app->bind('Vinelab\Auth\Contracts\StoreInterface', function(App $app){
			return new Store($app->make('cache'));
		});

		$this->app->bind(
			'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface',
			'Vinelab\Auth\Social\Providers\Twitter\OAuthSignature'
		);

		$this->app->bind(
			'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface',
			'Vinelab\Auth\Social\Providers\Twitter\OAuthConsumer'
		);

		$this->app->bind(
			'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface',
			'Vinelab\Auth\Social\Providers\Twitter\OAuthToken'
		);

		$this->app->bind(
			'Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthInterface',
			'Vinelab\Auth\Social\Providers\Twitter\OAuth'
		);

		$this->app->singleton('Vinelab\Auth\Contracts\ProvidersManagerInterface', function(App $app){
			return new ProvidersManager(
				$app->make('config'),
				$app->make('redirect'),
				$app->make('vinelab.httpclient'),
				$app->make('Vinelab\Auth\Contracts\StoreInterface'),
				$app->make('Vinelab\Auth\Contracts\ProfileInterface'),
				$app->make('Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface'),
				$app->make('Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthInterface'),
				$app->make('Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface'),
				$app->make('Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface'),
				$app->make('Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface')
			);
		});

		$this->app->bind('Vinelab\Auth\Contracts\ProfileInterface', 'Vinelab\Auth\Social\Profile');

		$this->app->bind(
			'Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface',
			'Vinelab\Auth\Social\Providers\Facebook\AccessToken');

		// Expose the Facade
		$this->app->bind('vinelab.socialauth', function(App $app){
			return new Auth($app->make('config'),
							$app->make('redirect'),
							$app->make('vinelab.httpclient'),
							$app->make('Vinelab\Auth\Contracts\ProvidersManagerInterface'));
		});

		$this->app->booting(function(){
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('SocialAuth', 'Vinelab\Auth\Facades\Auth');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
