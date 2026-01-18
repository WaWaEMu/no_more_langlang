<?php

namespace App\Http\Controllers;

class RobotsTxtController extends Controller
{
    /**
     * Generate robots.txt dynamically.
     */
    public function index()
    {
        $content = "User-agent: *\nDisallow:\n\nSitemap: " . url('/sitemap.xml');

        return response($content)
            ->header('Content-Type', 'text/plain');
    }
}
