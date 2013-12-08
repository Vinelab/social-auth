<?php namespace Vinelab\Auth;

use Illuminate\Support\ServiceProvider;

use Vinelab\Auth\Auth;
use Vinelab\Auth\Cache\Store;
use Vinelab\Auth\Social\Profile;
use Vinelab\Auth\Social\ProvidersManager;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('vinelab/social-auth');
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

		$this->app->bind('Vinelab\Auth\Contracts\StoreInterface', function($app){
			return new Store($app->make('cache'));
		});

		$this->app->singleton('Vinelab\Auth\Contracts\ProvidersManagerInterface', function($app){
			return new ProvidersManager($app->make('config'),
										$app->make('redirect'),
										$app->make('vinelab.httpclient'),
										$app->make('Vinelab\Auth\Contracts\StoreInterface'),
										$app->make('Vinelab\Auth\Contracts\ProfileInterface'),
										$app->make('Vinelab\Auth\Contracts\AccessTokenInterface'));
		});

		$this->app->bind('Vinelab\Auth\Contracts\ProfileInterface', function($app){
			return new Profile($app->make('config'));
		});

		$this->app->bind(
			'Vinelab\Auth\Contracts\AccessTokenInterface',
			'Vinelab\Auth\Social\AccessToken');

		// Expose the Facade
		$this->app->bind('vinelab.socialauth', function($app){
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