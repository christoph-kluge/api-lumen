<?php namespace App\Providers;

use App\Console\Commands\ReactPhpServeCommand;
use Illuminate\Support\ServiceProvider;

class ReactPhpServiceProvider extends ServiceProvider
{

  /**
   * Register any application services.
   * @return void
   */
  public function register()
  {
    $this->app->singleton(
      'api.serve',
      function () {
        return new ReactPhpServeCommand();
      }
    );

    $this->commands('api.serve');
  }
}
