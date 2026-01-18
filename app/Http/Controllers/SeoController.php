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
        $schema = $this->getSchema($path);

        return view('welcome', [
            'meta' => $meta,
            'schema' => $schema
        ]);
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

    /**
     * Generate JSON-LD structured data based on the current path.
     */
    private function getSchema($path)
    {
        $schemas = [];

        // Organization Schema - always included
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => '諾摩浪浪',
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'description' => '幫助流浪動物找到溫暖的家',
        ];

        // Pet Detail Page Schema
        if (preg_match('/^adopt\/(.+)$/', $path, $matches)) {
            $petIdOrSlug = $matches[1];
            // Try to find by slug first, then by ID
            $pet = Pet::with(['images', 'detail'])
                ->where('slug', $petIdOrSlug)
                ->orWhere('id', $petIdOrSlug)
                ->first();

            if ($pet) {
                // BreadcrumbList Schema
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => '首頁',
                            'item' => url('/')
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => '領養專區',
                            'item' => url('/adopt')
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $pet->name,
                            'item' => url('/adopt/' . ($pet->slug ?: $pet->id))
                        ]
                    ]
                ];

                // Enhanced Product Schema with additional properties
                $productSchema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Product',
                    'name' => $pet->name,
                    'description' => $pet->detail->adoption_description ?? '',
                    'image' => $pet->images->map(fn($img) => asset('storage/' . $img->path))->toArray(),
                    'brand' => [
                        '@type' => 'Brand',
                        'name' => '諾摩浪浪'
                    ],
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'TWD',
                        'availability' => 'https://schema.org/InStock'
                    ],
                    'category' => 'Pet Adoption',
                    'additionalProperty' => []
                ];

                // Add pet-specific properties
                if ($pet->type) {
                    $productSchema['additionalProperty'][] = [
                        '@type' => 'PropertyValue',
                        'name' => 'Species',
                        'value' => $pet->type
                    ];
                }

                if ($pet->age) {
                    $productSchema['additionalProperty'][] = [
                        '@type' => 'PropertyValue',
                        'name' => 'Age',
                        'value' => $pet->age
                    ];
                }

                if ($pet->gender) {
                    $productSchema['additionalProperty'][] = [
                        '@type' => 'PropertyValue',
                        'name' => 'Gender',
                        'value' => $pet->gender
                    ];
                }

                if ($pet->color) {
                    $productSchema['additionalProperty'][] = [
                        '@type' => 'PropertyValue',
                        'name' => 'Color',
                        'value' => $pet->color
                    ];
                }

                if ($pet->city) {
                    $productSchema['additionalProperty'][] = [
                        '@type' => 'PropertyValue',
                        'name' => 'Location',
                        'value' => $pet->city . ($pet->town ? ', ' . $pet->town : '')
                    ];
                }

                // Remove empty additionalProperty array if no properties were added
                if (empty($productSchema['additionalProperty'])) {
                    unset($productSchema['additionalProperty']);
                }

                $schemas[] = $productSchema;
            }
        }

        return $schemas;
    }
}
