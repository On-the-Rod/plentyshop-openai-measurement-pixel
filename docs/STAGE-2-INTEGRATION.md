# Stage 2 integration plan — OpenAI Measurement Pixel

## Verified in the target shop

The following has been tested in the live browser with CookieBar by Frontend Studios 3.1.2 and Ceres 5.0.48:

- After explicitly granting the OpenAI marketing consent, `window.ConsentManager.getConsents().marketing.openaiMeasurementPixel` is `true`.
- After revoking the consent in CookieBar and saving, the same value is `false`.
- The consent key is therefore registered and exposed through the Ceres ConsentManager as expected.
- `window.ceresStore` exists in the deployed shop.
- `window.ceresStore.subscribe` is a function.
- A Vuex subscription in the browser successfully observed the consent change mutation `toggleConsent`.
- The observed Vuex root state contains the `consents` module state. The user inspected the OpenAI consent value there and confirmed `false` after revocation and `true` after re-consent.

This verifies consent-state propagation and the availability of a live Vuex subscription in the tested browser session. It does not yet verify SDK loading, pixel initialization, measurement network requests, or revocation of an initialized SDK.

## Pixel configuration

- Pixel ID: `5zPBSSGnskgfYVgeGzQAfV`
- SDK URL: `https://bzrcdn.openai.com/sdk/oaiq.min.js`
- Consent key: `marketing.openaiMeasurementPixel`

## Runtime design

The deployed shop exposes a working Vuex store subscription. A runtime can subscribe to store mutations and, when the mutation is relevant, read the literal boolean consent value from the root state's `consents` module.

The implementation must still confirm the exact state path in the callback (expected: `state.consents.marketing.openaiMeasurementPixel`) and should also read the current value immediately after attaching the subscription. It must fail closed if the store, state path, or value is missing.

## Required runtime behavior

The production integration must satisfy all of these conditions:

1. No SDK script request, initialization, or measurement request before explicit consent.
2. Set the OpenAI SDK consent state to denied before initialization, then grant it only after the Ceres consent value is confirmed true.
3. Initialize at most once per page context; avoid adding duplicate SDK script elements or calling `init` more than once.
4. When consent changes to false, immediately call the SDK consent-revocation command if the SDK is available.
5. On a later transition back to true, re-enable consent without duplicating SDK loading or initialization.
6. Fail closed if the Ceres store, expected state path, or consent value is unavailable or throws.
7. Do not infer consent from a missing value. Only the literal boolean `true` grants permission.

## Integration decision still requiring verification

The Vuex subscription and the `toggleConsent` mutation are verified in the deployed browser. Before shipping runtime code, confirm the callback's exact state path and determine a reliable load point for the runtime relative to initialization of `window.ceresStore`. Do not invent a CookieBar callback or depend on undocumented CookieBar internals.

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

Do not enable the integration in production until every scenario passes.
