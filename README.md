# plentyshop-openai-measurement-pixel

Early development scaffold for plentyShop LTS / Ceres 5.0.48.

## Current status

**Stage 1: consent registration only.** The plugin registers **OpenAI Measurement Pixel**
as an optional consent in the existing \`marketing\` group through
\`ConsentRepositoryContract::registerConsent()\`.

It intentionally does **not** load the OpenAI SDK, initialize the pixel, or send
measurement events. This is not a production-ready tracking integration.

## Consent details

- Consent key: \`marketing.openaiMeasurementPixel\`
- Group: \`marketing\`
- Necessary: \`false\`
- Opt-out by default: \`false\`
- Pixel ID: not embedded at this stage.

## Next validation steps

1. Confirm the plugin builds in the target plugin set with Ceres 5.0.48 and IO
   5.0.48. The plugin metadata follows the Ceres 5.0.48 manifest convention
   and declares the IO dependency.
2. Confirm CookieBar 3.1.2 displays the consent under Marketing.
3. Confirm the active theme calls \`get_consent_scripts()\` with blocking enabled.
4. Add the SDK through a consent-gated script using
   \`data-cookie-consent="marketing.openaiMeasurementPixel"\`.
5. Ensure the OpenAI SDK receives consent=false before initialization and
   consent=true only after explicit approval; handle revocation and re-consent.
6. Verify in a clean browser session that no SDK or measurement requests occur
   before consent and initialization happens only once.

## References

- plentyShop consent registration:
  https://developers.plentymarkets.com/en-gb/developers/main/plentyshop-plugins/how-to-register-cookies.html
- Ceres 5.0.48 plugin manifest:
  https://github.com/plentymarkets/plugin-ceres/blob/5.0.48/plugin.json
- OpenAI Measurement Pixel:
  https://developers.openai.com/ads/measurement-pixel
