<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(SitemapService $sitemapService): Response
    {
        $xml = view('sitemap.xml', [
            'urls' => $sitemapService->build(),
        ])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'no-cache, max-age=0, must-revalidate');
    }
}
