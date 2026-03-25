<?php

declare(strict_types=1);

namespace URLCV\TechnicalInterviewPrep\Laravel;

use Illuminate\Support\ServiceProvider;

class TechnicalInterviewPrepServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'technical-interview-prep');
    }
}
