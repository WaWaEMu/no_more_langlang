<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate the sitemap.xml.
     */
    /**
     * Generate the sitemap.xml.
     */
    public function index(): Response
    {
        // Cache::remember(key, seconds, callback)
        // key: 'sitemap.xml' (unique name for this cache)
        // seconds: 60 * 60 * 24 (24 hours)
        return \Illuminate\Support\Facades\Cache::remember('sitemap.xml', 60 * 60 * 24, function () {
            // 1. Fetch data with Eager Loading
            // with(['images', 'detail']) 會一次把所有寵物的圖片與詳細資料撈出來，避免 N+1 問題
            $pets = Pet::with(['images', 'detail'])->get();
            $lastPet = Pet::orderBy('updated_at', 'desc')->first();

            // 2. Render view to string
            $content = view('sitemap', [
                'pets' => $pets,
                'lastmod' => $lastPet ? $lastPet->updated_at->toAtomString() : now()->toAtomString(),
            ])->render();

            // 3. Return response with correct content type
            return response($content)
                ->header('Content-Type', 'text/xml');
        });
    }
}
