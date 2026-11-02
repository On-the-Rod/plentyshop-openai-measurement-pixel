(function (window, document) {
    "use strict";

    var PIXEL_ID = "5zPBSSGnskgfYVgeGzQAfV";
    var SDK_URL = "https://bzrcdn.openai.com/sdk/oaiq.min.js";
    var CONSENT_GROUP = "marketing";
    var CONSENT_KEY = "openaiMeasurementPixel";
    var SDK_SCRIPT_ID = "openai-measurement-pixel-sdk";

    var store = null;
    var unsubscribe = null;
    var sdkRequested = false;
    var initialized = false;
    var lastConsent = null;

    function readConsent(rootState) {
        var moduleState;
        var consentGroups;

        if (!rootState || !rootState.consents) {
            return null;
        }

        moduleState = rootState.consents;
        consentGroups = moduleState.consents;

        if (!consentGroups || !consentGroups[CONSENT_GROUP]) {
            return null;
        }

        if (typeof consentGroups[CONSENT_GROUP][CONSENT_KEY] !== "boolean") {
            return null;
        }

        return consentGroups[CONSENT_GROUP][CONSENT_KEY];
    }

    function ensureQueue() {
        if (typeof window.oaiq === "function") {
            return;
        }

        var queue = function () {
            queue.q.push(arguments);
        };

        queue.q = [];
        window.oaiq = queue;
    }

    function loadAndInitialize() {
        var script;

        if (initialized) {
            return;
        }

        ensureQueue();

        // Fail closed by queueing denied consent before initialization.
        window.oaiq("consent", false);
        window.oaiq("init", {
            pixelId: PIXEL_ID,
            debug: false
        });

        initialized = true;

        if (sdkRequested || document.getElementById(SDK_SCRIPT_ID)) {
            sdkRequested = true;
            return;
        }

        script = document.createElement("script");
        script.id = SDK_SCRIPT_ID;
        script.async = true;
        script.src = SDK_URL;
        script.onerror = function () {
            // Do not retry automatically. The consent observer remains active.
            // A later explicit consent transition can re-grant consent, but it
            // will not inject a second SDK script into this page context.
            script.setAttribute("data-load-error", "true");
        };

        sdkRequested = true;
        (document.head || document.getElementsByTagName("head")[0]).appendChild(script);
    }

    function applyConsent(consent) {
        if (consent !== true && consent !== false) {
            return;
        }

        if (lastConsent === consent) {
            return;
        }

        lastConsent = consent;

        if (consent === true) {
            loadAndInitialize();
            if (typeof window.oaiq === "function") {
                window.oaiq("consent", true);
            }
            return;
        }

        // If the SDK has not been initialized, do not request it at all.
        if (initialized && typeof window.oaiq === "function") {
            window.oaiq("consent", false);
        }
    }

    function attachStore() {
        if (unsubscribe || !window.ceresStore ||
            typeof window.ceresStore.subscribe !== "function") {
            return false;
        }

        store = window.ceresStore;

        // Subscribe before the initial read so no consent change can fall
        // into a gap between reading the current value and attaching listener.
        unsubscribe = store.subscribe(function (mutation, rootState) {
            // Read the actual state after every mutation. This avoids relying
            // on a CookieBar-specific callback or undocumented mutation payload.
            applyConsent(readConsent(rootState));
        });

        // Vuex module state is nested under rootState.consents.consents.
        applyConsent(readConsent(store.state));

        return true;
    }

    function start() {
        var attempts = 0;
        var maxAttempts = 300;
        var poll;

        if (attachStore()) {
            return;
        }

        // The runtime may be included before Ceres finishes creating its store.
        poll = window.setInterval(function () {
            attempts += 1;

            if (attachStore() || attempts >= maxAttempts) {
                window.clearInterval(poll);
            }
        }, 100);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", start);
    } else {
        start();
    }
}(window, document));
