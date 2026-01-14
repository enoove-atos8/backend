<?php

namespace Application\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Infrastructure\Services\External\WhatsApp\EvolutionApiService;
use Infrastructure\Services\External\WhatsApp\Interfaces\WhatsAppServiceInterface;
use Infrastructure\Services\External\WhatsApp\MetaCloudApiService;
use Infrastructure\Services\External\WhatsApp\TwilioService;
use Infrastructure\Services\External\WhatsApp\ZApi\ZApiService;

class WhatsAppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WhatsAppServiceInterface::class, function ($app) {
            $driver = config('whatsapp.driver', 'evolution');

            return match ($driver) {
                'meta' => new MetaCloudApiService,
                'evolution' => new EvolutionApiService,
                'zapi' => new ZApiService,
                'twilio' => new TwilioService,
                default => throw new \InvalidArgumentException("WhatsApp driver [{$driver}] não suportado."),
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
