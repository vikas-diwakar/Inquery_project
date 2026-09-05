<?php

namespace App\Helpers;

class SeoHelper
{
    /**
     * Format page title for SEO.
     */
    public static function title(?string $pageTitle = null): string
    {
        $siteName = config('seo.site_name', 'PropDrip');
        $defaultTitle = config('seo.default_title', 'PropDrip - Real Estate Inquiry & Lead Management SaaS');

        if (empty($pageTitle)) {
            return $defaultTitle;
        }

        return $pageTitle . config('seo.title_separator', ' | ') . $siteName;
    }

    /**
     * Get meta description.
     */
    public static function description(?string $description = null): string
    {
        if (!empty($description)) {
            return e(mb_strimwidth(strip_tags($description), 0, 160, '...'));
        }

        return config('seo.default_description');
    }

    /**
     * Get absolute canonical URL.
     */
    public static function canonical(?string $url = null): string
    {
        return $url ?: request()->url();
    }

    /**
     * Format keywords string.
     */
    public static function keywords(array $keywords = []): string
    {
        $default = config('seo.default_keywords', []);
        $merged = array_unique(array_merge($keywords, $default));

        return implode(', ', $merged);
    }

    /**
     * Get social share image URL (Open Graph / Twitter).
     */
    public static function image(?string $imagePath = null): string
    {
        if (empty($imagePath)) {
            return url(config('seo.default_image'));
        }

        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        return url($imagePath);
    }

    /**
     * Generate Schema.org JSON-LD Structured Data script tag.
     */
    public static function schemaOrg(string $type = 'SoftwareApplication', array $customData = []): string
    {
        $baseUrl = url('/');

        if ($type === 'SoftwareApplication') {
            $data = [
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => 'PropDrip',
                'operatingSystem' => 'Web',
                'applicationCategory' => 'BusinessApplication',
                'url' => $baseUrl,
                'description' => config('seo.default_description'),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'INR'
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'PropDrip SaaS',
                    'url' => $baseUrl,
                    'logo' => url('/images/logo.png')
                ]
            ];
        } elseif ($type === 'RealEstateListing') {
            $data = array_merge([
                '@context' => 'https://schema.org',
                '@type' => 'RealEstateListing',
                'url' => request()->url(),
            ], $customData);
        } else {
            $data = array_merge([
                '@context' => 'https://schema.org',
                '@type' => $type,
            ], $customData);
        }

        return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
}
