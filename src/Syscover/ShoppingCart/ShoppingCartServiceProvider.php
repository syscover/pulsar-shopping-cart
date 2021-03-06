<?php namespace Syscover\ShoppingCart;

use Illuminate\Support\ServiceProvider;

class ShoppingCartServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
        // register routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

		// register tests
		$this->publishes([
			__DIR__ . '/../../tests/Feature' => base_path('/tests/Feature')
		], 'tests');

        // register config files
        $this->publishes([
            __DIR__ . '/../../config/pulsar-shopping_cart.php' => config_path('pulsar-shopping_cart.php')
        ]);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('cart-provider', CartProvider::class);
	}
}