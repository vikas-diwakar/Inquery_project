<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Generate dynamic sitemap.xml for Google indexing.
     */
    public function sitemap(): Response
    {
        $baseUrl = url('/');
        $urls = [];

        // Static Pages
        $urls[] = [
            'loc' => $baseUrl,
            'lastmod' => now()->toIso8601String(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];

        $urls[] = [
            'loc' => route('login'),
            'lastmod' => now()->toIso8601String(),
            'changefreq' => 'monthly',
            'priority' => '0.8'
        ];

        $urls[] = [
            'loc' => route('company.register'),
            'lastmod' => now()->toIso8601String(),
            'changefreq' => 'monthly',
            'priority' => '0.8'
        ];

        // Public Project Inquiry Forms (bypass tenant scope for global sitemap)
        try {
            $projects = Project::withoutGlobalScopes()->get();

            foreach ($projects as $project) {
                $urls[] = [
                    'loc' => route('public.inquiry.form', ['project' => $project->id]),
                    'lastmod' => ($project->updated_at ?? now())->toIso8601String(),
                    'changefreq' => 'weekly',
                    'priority' => '0.9'
                ];
            }
        } catch (\Exception $e) {
            // Log or ignore if DB is uninitialized
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $url['priority'] . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8'
        ]);
    }

    /**
     * Generate dynamic robots.txt for Googlebot.
     */
    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /dashboard\n";
        $content .= "Disallow: /inquiries\n";
        $content .= "Disallow: /projects\n";
        $content .= "Disallow: /settings\n";
        $content .= "Disallow: /subscription\n";
        $content .= "Disallow: /users\n\n";
        $content .= "Sitemap: {$sitemapUrl}\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ]);
    }
}
