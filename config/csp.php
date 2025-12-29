<?php

return [
    /*
     * A policy will determine which CSP headers will be set. A valid policy is
     * any class that extends `Spatie\Csp\Policies\Policy`.
     */
    'policy' => \App\Http\Policies\CustomCspPolicy::class,

    /*
     * This policy which will be put in report only mode. This is great for testing out
     * a new policy or changes to an existing one.
     */
    'report_only_policy' => '',

    /*
     * All violations of the policy will be reported to this URI.
     * A great service you could use for this is https://report-uri.com/
     *
     * You can override this setting by calling `reportTo` on your policy.
     */
    'report_uri' => env('CSP_REPORT_URI', ''),

    /*
     * Headers will only be added if this setting is set to true.
     */
    'enabled' => true,

    /*
     * The class responsible for generating the nonce used in the CSP headers.
     *
     * A valid nonce generator is any class that implements `Spatie\Csp\Nonce\NonceGenerator`.
     */
    'nonce_generator' => Spatie\Csp\Nonce\RandomString::class,
];
