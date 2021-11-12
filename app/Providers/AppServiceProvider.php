<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Modules\Repositories\Interfaces\Generator',
            'App\Modules\Repositories\SampleGenerator'
        );

        $this->app->bind(
            'App\Modules\Repositories\Interfaces\Selector',
            'App\Modules\Repositories\Selector'
        );

        $this->app->bind(
            'App\Modules\Repositories\Interfaces\QuestionDisplay',
            'App\Modules\Repositories\SampleQuestionDisplay'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
