<?php namespace Vinelab\Auth;

use Illuminate\Support\ServiceProvider;

use Najem\Models\Entities\User as UserEntity;
use Najem\Models\Entities\SocialAccount as SocialAccountEntity;

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
		$this->package('vinelab/auth');

		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['vinelab.social.auth'] = $this->app->share(function($app){
			return new Social($app['config'],
							  $app['cache'],
							  $app['redirect'],
							  $app['vinelab.httpclient']);
		});

		$this->app->booting(function(){
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('SocialAuth', 'Vinelab\Auth\Social');
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