<?php

declare(strict_types=1);

namespace URLCV\TechnicalInterviewPrep\Laravel;

use Illuminate\Support\ServiceProvider;

class TechnicalInterviewPrepServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'technical-interview-prep');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../resources/public/js/tip-js-runner.worker.js' => public_path('js/tip-js-runner.worker.js'),
                __DIR__ . '/../../resources/public/js/tip-py-runner.worker.js' => public_path('js/tip-py-runner.worker.js'),
            ], 'laravel-assets');
        }
    }
}
