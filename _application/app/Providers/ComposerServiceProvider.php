<?php
/**
 * Created by PhpStorm.
 * User: EL KADIRI
 * Date: 05/03/2018
 * Time: 16:51
 */

namespace App\Providers;


use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        View::composer(
            'auth.register', 'App\Http\ViewComposers\CarrierComposer'
        );

        // Using Closure based composers...
//        View::composer('dashboard', function ($view) {
//            //
//        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
