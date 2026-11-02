# Stage 2 integration plan — OpenAI Measurement Pixel

## Verified in the target shop

Tested in the live browser with CookieBar by Frontend Studios 3.1.2 and Ceres 5.0.48:

- The consent value is readable through `window.ConsentManager.getConsents().marketing.openaiMeasurementPixel`.
- `window.ceresStore` exists and `window.ceresStore.subscribe` is a function.
- A Vuex subscription observed the `toggleConsent` mutation when the OpenAI marketing consent was changed.
- The user inspected the Ceres store and confirmed the nested consent value was `false` after revocation and `true` after re-consent.

This verifies consent-state propagation and the availability of a live Vuex subscription in the tested browser session. It does not verify SDK loading, pixel initialization, measurement requests, or revocation of an initialized SDK.

## Runtime prototype added

File: `resources/js/openai-measurement-pixel.js`

The runtime prototype and its Twig import template have been committed. The ServiceProvider listens for `IO.Resources.Import` and calls `ResourceContainer::addScriptTemplate('OpenAiMeasurementPixel::content.OpenAiMeasurementPixel')`. The Twig template includes the runtime from the plugin resource path with `defer`. This wiring follows the official plentyShop cookbook example but has **not been deployed or browser-tested in the target shop**.

It is designed to:

- wait for the Ceres store if the runtime is loaded before it exists;
- subscribe to Vuex mutations and read the nested state at `rootState.consents.consents.marketing.openaiMeasurementPixel`;
- grant permission only for a literal boolean `true`;
- avoid requesting the OpenAI SDK before consent;
- queue a denied consent state before pixel initialization, then queue consent true;
- send consent false on a later revocation and true on re-consent;
- load the SDK and initialize only once per page context.

The precise Vuex state path is inferred from the inspected Ceres module state and must be confirmed in a controlled browser test before deployment.

## Pixel configuration

- Pixel ID: `5zPBSSGnskgfYVgeGzQAfV`
- SDK URL: `https://bzrcdn.openai.com/sdk/oaiq.min.js`
- Consent key: `marketing.openaiMeasurementPixel`

## Required runtime behavior and caveats

1. No SDK script request, initialization, or measurement request before explicit consent.
2. Queue consent denied before initialization; grant only after Ceres confirms consent.
3. Initialize at most once per page context.
4. On revocation, immediately queue/set SDK consent false.
5. On re-consent, grant again without duplicate SDK loading or initialization.
6. Fail closed if the store, expected state path, or consent value is unavailable.
7. Only literal boolean `true` grants permission.
8. Confirm OpenAI SDK consent-command semantics against the current official documentation before production use.
9. Decide and verify the production template asset-loading mechanism and script ordering before deployment.
10. The current prototype does not automatically retry an SDK network load failure.

## Acceptance tests before production

Use a clean browser profile and inspect both Console and Network:

| Scenario | Expected result |
|---|---|
| No consent response yet | No SDK or measurement request |
| Reject all | No SDK or measurement request |
| Marketing accepted | SDK loaded once; initialization once; consent granted |
| Marketing revoked after acceptance | Consent immediately denied; no further measurement |
| Marketing accepted again | Consent granted again; no duplicate SDK/init |
| Navigate to another shop page | No duplicate SDK/init; state remains respected |
| SDK network load fails | No duplicate script injection; error is visible for diagnosis |

Do not enable the integration in production until every scenario passes.
