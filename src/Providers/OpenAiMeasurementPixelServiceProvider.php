<?php

namespace OpenAiMeasurementPixel\Providers;

use Plenty\Modules\Webshop\Consent\Contracts\ConsentRepositoryContract;
use Plenty\Plugin\ServiceProvider;

/**
 * Stage 1: register the OpenAI Measurement Pixel as an optional marketing
 * consent. This provider does not load or initialize the SDK.
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
}
