# plentyshop-openai-measurement-pixel

Early development scaffold for plentyShop LTS / Ceres 5.0.48.

## Current status

**Stage 1: consent registration only.** The plugin registers an optional
consent named **OpenAI Measurement Pixel** in the existing `marketing` group,
using the documented `ConsentRepositoryContract::registerConsent()` API.

This stage intentionally does **not** load the OpenAI SDK, initialize the
pixel, or send measurement events. Do not consider it a production-ready
tracking integration.

## Consent details

- Consent key: `marketing.openaiMeasurementPixel`
- Group: `marketing`
- Necessary: `false`
- Opt-out by default: `false`
- Pixel ID: configured only in the later frontend integration stage.

## Required validation before production

1. Build the plugin against the exact Ceres 5.0.48 and IO versions in the
   target plugin set. Confirm the dependency constraint in `plugin.json`
   against the installed plugin versions.
2. Confirm CookieBar 3.1.2 displays this consent under Marketing.
3. Confirm the active shop template runs the native `get_consent_scripts()`
   processing with blocking enabled.
4. Add the OpenAI SDK only as a consent-gated script using
   `marketing.openaiMeasurementPixel`.
5. Set SDK consent to false before initialization, then enable only after
   marketing consent; handle revocation and re-consent.
6. Verify in a clean browser session that no SDK or measurement requests occur
   before consent and that initialization happens only once.

## References

- [plentyShop consent registration](https://developers.plentymarkets.com/en-gb/developers/main/plentyshop-plugins/how-to-register-cookies.html)
- [plentyShop plugin definition](https://developers.plentymarkets.com/en-gb/developers/main/plugin-definition.html)
- [OpenAI Measurement Pixel](https://developers.openai.com/ads/measurement-pixel)
