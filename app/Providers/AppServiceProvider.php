<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        // view()->share('color',$color);
        View::composer('layouts.newLayout', function ($view) {
        $color = DB::table('sidebar')->where('id',1)->first('color');
            $view->with('color',$color);
        });
        
        $lifetime = env('LIFETIME', 5);
        config(['session.lifetime' => $lifetime]);
    }
}
