<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default SEO Configurations
    |--------------------------------------------------------------------------
    |
    | Default meta tags, title template, and fallback metadata for PropDrip SaaS.
    |
    */

    'site_name' => env('SEO_SITE_NAME', 'PropDrip'),

    'default_title' => env('SEO_DEFAULT_TITLE', 'PropDrip - Real Estate Inquiry & Lead Management SaaS'),

    'title_separator' => ' | ',

    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'PropDrip is a modern Real Estate Builder & Developer SaaS platform to capture property buyer inquiries, manage project QR codes, automate WhatsApp brochures, and nurture leads.'
    ),

    'default_keywords' => [
        'real estate saas',
        'property inquiry management',
        'real estate builder crm',
        'whatsapp brochure automation',
        'lead drip automation',
        'real estate lead tracking',
        'propdrip'
    ],

    'default_author' => env('SEO_DEFAULT_AUTHOR', 'PropDrip SaaS'),

    'default_image' => env('SEO_DEFAULT_IMAGE', '/images/propdrip-og-cover.png'),

    'twitter_handle' => env('SEO_TWITTER_HANDLE', '@propdrip'),

    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION', ''),
];
