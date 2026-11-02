# plentyshop-openai-measurement-pixel

Early development scaffold for plentyShop LTS / Ceres 5.0.48.

## Current status

**Stage 1: consent registration implemented and verified in the target shop.** The plugin registers **OpenAI Measurement Pixel** as an optional consent in the existing `marketing` group through `ConsentRepositoryContract::registerConsent()`.

The shop was tested with CookieBar by Frontend Studios 3.1.2:

- After granting the OpenAI marketing consent, `window.ConsentManager.getConsents().marketing.openaiMeasurementPixel` returned `true`.
- After revoking the consent and saving, it returned `false`.

**Stage 2: runtime prototype and script-import wiring committed; shop deployment and browser tests are pending.** The plugin now imports its runtime through the documented `IO.Resources.Import` / `ResourceContainer::addScriptTemplate()` mechanism. The runtime is not yet verified in the target shop and must not be treated as production-ready.

See [docs/STAGE-2-INTEGRATION.md](docs/STAGE-2-INTEGRATION.md) for the verified consent behavior, required runtime behavior, and acceptance tests.

## Consent details

- Consent key: `marketing.openaiMeasurementPixel`
- Group: `marketing`
- Necessary: `false`
- Opt-out by default: `false`
- Pixel ID: `5zPBSSGnskgfYVgeGzQAfV` (reserved for the integration stage; not embedded in runtime code)

## References

- plentyShop consent registration:
  https://developers.plentymarkets.com/en-gb/developers/main/plentyshop-plugins/how-to-register-cookies.html
- Ceres 5.0.48 plugin manifest:
  https://github.com/plentymarkets/plugin-ceres/blob/5.0.48/plugin.json
- OpenAI Measurement Pixel:
  https://developers.openai.com/ads/measurement-pixel
