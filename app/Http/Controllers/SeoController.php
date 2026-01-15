<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /**
     * Handle the SPA entry point and inject meta tags.
     */
    public function index(Request $request)
    {
        $path = $request->path();
        $meta = $this->getMeta($path);

        return view('welcome', ['meta' => $meta]);
    }

    /**
     * Generate meta tags based on the current path.
     */
    private function getMeta($path)
    {
        // Default meta tags for the site
        $defaultTitle = '諾摩浪浪 - 幫助流浪動物找到溫暖的家';
        $defaultDescription = '諾摩浪浪是一個致力於幫助流浪動物尋找領養家庭的平台。在這裡，您可以瀏覽待領養的毛孩，或是發布領養資訊。';
        $defaultImage = asset('images/og-image.jpg'); // Ensure this exists or use a placeholder
        $url = url($path);

        $meta = [
            'title' => $defaultTitle,
            'description' => $defaultDescription,
            'image' => $defaultImage,
            'url' => $url,
        ];

        // Specific logic for Pet Detail Page: /adopt/{id}
        // We use regex to match the pattern and extract the ID
        if (preg_match('/^adopt\/(\d+)$/', $path, $matches)) {
            $petId = $matches[1];
            $pet = Pet::with(['images', 'detail'])->find($petId);

            if ($pet) {
                $meta['title'] = "領養 {$pet->name} - {$pet->title} | 諾摩浪浪";
                // Use the adoption description if available, otherwise fallback to default
                $meta['description'] = $pet->detail->adoption_description ?? $defaultDescription;

                // Use the first pet image as the OG image
                if ($pet->images->count() > 0) {
                    $meta['image'] = asset('storage/' . $pet->images->first()->path);
                }
            }
        }

        return $meta;
    }
}
