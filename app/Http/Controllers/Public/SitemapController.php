<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $pages = [
            ['url' => $baseUrl.'/',          'priority' => '1.0',  'changefreq' => 'weekly',  'lastmod' => now()->toDateString()],
            ['url' => $baseUrl.'/about',     'priority' => '0.8',  'changefreq' => 'monthly', 'lastmod' => now()->toDateString()],
            ['url' => $baseUrl.'/services',  'priority' => '0.9',  'changefreq' => 'monthly', 'lastmod' => now()->toDateString()],
            ['url' => $baseUrl.'/projects',  'priority' => '0.7',  'changefreq' => 'weekly',  'lastmod' => now()->toDateString()],
            ['url' => $baseUrl.'/contact',   'priority' => '0.8',  'changefreq' => 'yearly',  'lastmod' => now()->toDateString()],
        ];

        $xml = view('sitemap', compact('pages'))->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
