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
        $defaultTitle = __('SEO Default Title');
        $defaultDescription = __('SEO Default Description');
        $defaultImage = asset('images/og-image.jpg'); // Ensure this exists or use a placeholder
        $url = url($path);

        $meta = [
            'title' => $defaultTitle,
            'description' => $defaultDescription,
            'image' => $defaultImage,
            'image_alt' => $defaultTitle,
            'url' => $url,
        ];

        // Specific logic for Pet List Page: /adopt
        if ($path === 'adopt') {
            $meta['title'] = __('SEO Adopt Title');
            $meta['description'] = __('SEO Adopt Description');
            $meta['image_alt'] = __('SEO Adopt Name');
        }

        // Specific logic for Pet Detail Page: /adopt/{id}
        // We use regex to match the pattern and extract the ID
        if (preg_match('/^adopt\/(\d+)$/', $path, $matches)) {
            $petId = $matches[1];
            $pet = Pet::with(['images', 'detail'])->find($petId);

            if ($pet) {
                $meta['title'] = __("SEO Pet Title", ['name' => $pet->name, 'title' => $pet->title]);
                // Use the adoption description if available, otherwise fallback to default
                $meta['description'] = $pet->detail->adoption_description ?? $defaultDescription;
                $meta['image_alt'] = __("SEO Pet Image Alt", ['name' => $pet->name]);

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
            'name' => __('App Name'),
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'description' => __('SEO Default Description'),
        ];

        // Adopt List Page Schema
        if ($path === 'adopt') {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => __('SEO Home Name'),
                        'item' => url('/')
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => __('SEO Adopt Name'),
                        'item' => url('/adopt')
                    ]
                ]
            ];
        }

        // Pet Detail Page Schema
        if (preg_match('/^adopt\/(\d+)$/', $path, $matches)) {
            $petId = $matches[1];
            $pet = Pet::with(['images', 'detail'])
                ->find($petId);

            if ($pet) {
                // BreadcrumbList Schema
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => __('SEO Home Name'),
                            'item' => url('/')
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => __('SEO Adopt Name'),
                            'item' => url('/adopt')
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $pet->name,
                            'item' => url('/adopt/' . $pet->id)
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
                        'name' => __('App Name')
                    ],
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'TWD',
                        'availability' => $this->mapStatusToAvailability($pet->status)
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

    /**
     * Map pet status to Schema.org availability.
     */
    private function mapStatusToAvailability($status)
    {
        $map = [
            'available' => 'https://schema.org/InStock',
            'pending' => 'https://schema.org/InStock',
            'paused' => 'https://schema.org/OutOfStock',
            'adopted' => 'https://schema.org/OutOfStock',
        ];

        return $map[$status] ?? 'https://schema.org/InStock';
    }
}
