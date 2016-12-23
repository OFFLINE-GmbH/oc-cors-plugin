<?php

namespace OFFLINE\CORS\Classes;

use October\Rain\Support\ServiceProvider as BaseServiceProvider;
use OFFLINE\CORS\Models\Settings;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CorsService::class, function ($app) {
            return new CorsService($this->getSettings());
        });
    }

    /**
     * Return default Settings
     */
    protected function getSettings()
    {
        $supportsCredentials = (bool)Settings::get('supportsCredentials', false);
        $maxAge              = (int)Settings::get('maxAge', 0);
        $allowedOrigins      = $this->getValues(Settings::get('allowedOrigins', []));
        $allowedHeaders      = $this->getValues(Settings::get('allowedHeaders', []));
        $allowedMethods      = $this->getValues(Settings::get('allowedMethods', []));
        $exposedHeaders      = $this->getValues(Settings::get('exposedHeaders', []));
        $hosts               = $this->getValues(Settings::get('hosts', []));

        return compact(
            'supportsCredentials',
            'allowedOrigins',
            'allowedHeaders',
            'allowedMethods',
            'exposedHeaders',
            'maxAge',
            'hosts'
        );
    }

    /**
     * Extract the repeater field values.
     *
     * @param array $values
     *
     * @return array
     */
    protected function getValues(array $values)
    {
        return collect($values)->pluck('value')->toArray();
    }
}