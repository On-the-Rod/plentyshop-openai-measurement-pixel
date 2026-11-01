# Stage 2 integration plan — OpenAI Measurement Pixel

## Verified in the target shop

The following has been tested in the live browser with CookieBar by Frontend Studios 3.1.2 and Ceres 5.0.48:

- After explicitly granting the OpenAI marketing consent, `window.ConsentManager.getConsents().marketing.openaiMeasurementPixel` is `true`.
- After revoking the consent in CookieBar and saving, the same value is `false`.
- The consent key is therefore registered and exposed through the Ceres ConsentManager as expected.

This verifies consent-state propagation only. It does not yet verify SDK loading, pixel initialization, network requests, or revocation of an initialized SDK.

## Pixel configuration

- Pixel ID: `5zPBSSGnskgfYVgeGzQAfV`
- SDK URL: `https://bzrcdn.openai.com/sdk/oaiq.min.js`
- Consent key: `marketing.openaiMeasurementPixel`

## Required runtime behavior

The production integration must satisfy all of these conditions:

1. No SDK script request, initialization, or measurement request before explicit consent.
2. Set the OpenAI SDK consent state to denied before initialization, then grant it only after the Ceres consent value is confirmed true.
3. Initialize at most once per page context; avoid adding duplicate SDK script elements or calling `init` more than once.
4. When consent changes to false, immediately call the SDK consent-revocation command if the SDK is available.
5. On a later transition back to true, re-enable consent without duplicating SDK loading or initialization.
6. Fail closed if `ConsentManager`, `getConsents()`, or the expected consent key is unavailable or throws.
7. Do not infer consent from a missing value. Only the literal boolean `true` grants permission.

## Integration decision still requiring verification

The shop test confirms the current consent value can be read, but it does not establish a documented notification/event API for changes made in CookieBar. Before shipping runtime code, verify how Ceres 5.0.48 exposes consent changes to frontend code (or whether a narrowly scoped state observer is required). Do not invent a CookieBar callback or depend on undocumented CookieBar internals.

The Ceres 5.0.48 store module uses `ConsentManager.getConsents()` to refresh consent state and `ConsentManager.setResponse(...)` when storing consent. The exact supported frontend lifecycle hook for observing subsequent changes must still be established against the deployed theme/plugin runtime.

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
