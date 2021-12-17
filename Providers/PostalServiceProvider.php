<?php

namespace Modules\Postal\Providers;

use App\Mailbox;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class PostalServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
        $this->macros();
    }

    /**
     * Module macros.
     */
    public function macros()
    {
        \MacroableModels::addMacro(Mailbox::class, 'getPostalEndpoint', function() {
            return route('postal.endpoint.http', [
                'mailbox_id' => $this->id,
            ]);
        });
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        $incomingHttp = (int) \App\Option::get('postal.incoming.http.id');

        \Eventy::addFilter('mailbox.in_protocols', function($protocols, $mailbox) use ($incomingHttp) {
            $protocols[$incomingHttp] = 'postal-http';

            return $protocols;
        }, 20, 2);

        \Eventy::addFilter('mailbox.in_protocols.display_names', function($protocols, $mailbox) use ($incomingHttp)  {
            $protocols[$incomingHttp] = 'Postal - HTTP endpoint';

            return $protocols;
        }, 20, 2);

        \Eventy::addFilter('mailbox.in_active', function($active, $mailbox) use ($incomingHttp)  {
            return $mailbox->in_protocol !== $incomingHttp ? $active : true;
        }, 20, 2);

        \Eventy::addFilter('mailbox.fetch_test', function($response, $mailbox) use ($incomingHttp)  {
            if ($mailbox->in_protocol !== $incomingHttp) {
                return $response;
            }

            $response['msg'] = 'Connection should be setup manually in Postal.';
            $response['status'] = 'success';

            return $response;
        }, 20, 2);

        \Eventy::addFilter('mailbox.connection_incoming.settings', function($template, $id) use ($incomingHttp)  {
            return $id !== $incomingHttp ? $template : 'postal::mailboxes.partials.connection_incoming.postal-http';
        }, 20, 2);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('postal.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'postal'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/postal');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/postal';
        }, \Config::get('view.paths')), [$sourcePath]), 'postal');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
