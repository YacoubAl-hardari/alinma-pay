<?php

namespace AlinmaPay;

use AlinmaPay\Contracts\PaymentGatewayInterface;
use AlinmaPay\Contracts\SignatureGeneratorInterface;
use AlinmaPay\Contracts\WebhookHandlerInterface;
use AlinmaPay\Services\PaymentService;
use AlinmaPay\Services\SignatureService;
use AlinmaPay\Services\WebhookService;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class AlinmaPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/alinmapay.php', 'alinmapay');

        $this->app->singleton(SignatureGeneratorInterface::class, function ($app) {
            return new SignatureService();
        });
           $this->app->singleton(WebhookHandlerInterface::class, function ($app) {
        return new WebhookService(
            signatureService: $app->make(\AlinmaPay\Contracts\SignatureGeneratorInterface::class),
            logger: $app->bound(LoggerInterface::class) ? $app->make(LoggerInterface::class) : null
        );
    });

        $this->app->singleton(PaymentGatewayInterface::class, function ($app) {
            $config = $app['config']['alinmapay'];

            return new PaymentService(
                signatureService: $app->make(SignatureGeneratorInterface::class),
                endpoint: $config['endpoints'][$config['environment']],
                terminalId: $config['terminal_id'],
                terminalPassword: $config['terminal_password'],
                merchantKey: $config['merchant_key'],
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $configSource = __DIR__ . '/Config/alinmapay.php';
            if (file_exists($configSource)) {
                $this->publishes([
                    $configSource => config_path('alinmapay.php'),
                ], 'alinmapay-config');
            }
        }
    }
}
