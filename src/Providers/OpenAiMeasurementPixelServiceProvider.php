<?php

namespace OpenAiMeasurementPixel\Providers;

use IO\Helper\ResourceContainer;
use Plenty\Modules\Webshop\Consent\Contracts\ConsentRepositoryContract;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Templates\Twig;

/**
 * Registers the OpenAI Measurement Pixel consent and imports its runtime.
 */
class OpenAiMeasurementPixelServiceProvider extends ServiceProvider
{
    const PRIORITY = 0;

    public function register()
    {
        /** @var ConsentRepositoryContract $consentRepository */
        $consentRepository = pluginApp(ConsentRepositoryContract::class);

        $consentRepository->registerConsent(
            'openaiMeasurementPixel',
            'OpenAI Measurement Pixel',
            function () {
                return [
                    'description' => 'Measures conversions attributable to OpenAI advertisements.',
                    'provider' => 'OpenAI',
                    'lifespan' => 'See OpenAI privacy information.',
                    'policyUrl' => 'https://openai.com/policies/privacy-policy/',
                    'group' => 'marketing',
                    'necessary' => false,
                    'isOptOut' => false,
                    'cookieNames' => ['__oppref', '__obref']
                ];
            }
        );
    }

    /**
     * Import the pixel runtime into plentyShop LTS through its resource event.
     */
    public function boot(Twig $twig, Dispatcher $eventDispatcher)
    {
        $eventDispatcher->listen('IO.Resources.Import', function (ResourceContainer $container) {
            $container->addScriptTemplate('OpenAiMeasurementPixel::content.OpenAiMeasurementPixel');
        }, self::PRIORITY);
    }
}
