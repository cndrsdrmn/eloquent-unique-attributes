<?php

namespace Cndrsdrmn\EloquentUniqueAttributes;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'unique_attributes');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => config_path('unique_attributes.php'),
            ], 'unique-attributes-config');
        }
    }

    /**
     * Get config path of unique attributes.
     */
    protected function configPath(): string
    {
        return __DIR__.'/../config/unique_attributes.php';
    }
}
