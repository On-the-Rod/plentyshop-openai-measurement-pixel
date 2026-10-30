<?php

namespace OpenAiMeasurementPixel\\Providers;

use Plenty\\Modules\\Webshop\\Consent\\Contracts\\ConsentRepositoryContract;
use Plenty\\Plugin\\ServiceProvider;

/**
 * Registers the OpenAI Measurement Pixel in the native plentyShop consent
 * repository. This first-stage plugin deliberately does not load the pixel.
 */
class OpenAiMeasurementPixelServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var ConsentRepositoryContract $consentRepository */
        $consentRepository = pluginApp(ConsentRepositoryContract::class);

        $consentRepository->registerConsent(
            'openaiMeasurementPixel',
            'OpenAI Measurement Pixel',
            [
                'description' => 'Measures the effectiveness of OpenAI advertising.',
                'provider' => 'OpenAI',
                'lifespan' => 'According to the provider information',
                'policyUrl' => 'https://openai.com/policies/privacy-policy/',
                'group' => 'marketing',
                'necessary' => false,
                'isOptOut' => false,
                'cookieNames' => ['__oppref', '__obref']
            ]
        );
    }
}
