<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Pet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_attach_sendable_cities_to_pet() {
        /* Arrange */
        $pet = Pet::factory()->create();

        /* Act */
        $pet->attachSendableCities(['台北市', '新北市']);

        $this->assertDatabaseHas('pet_sendable_cities', [
            'pet_id' => $pet->id,
            'city' => '台北市'
        ]);

        /* Assert */
        $cities = $pet->sendableCities()->pluck('city')->toArray();
        $this->assertEquals(['台北市', '新北市'], $cities);
    }

    public function test_store_images_to_pet() {
        /* Arrange */
        Storage::fake('public');
        $pet = Pet::factory()->create();
        $fakeImage1 = UploadedFile::fake()->image('dog1.jpg');
        $fakeImage2 = UploadedFile::fake()->image('dog2.jpg');

        $today = now()->format('Y_m_d');
        $expectedPath1 = "images/{$today}/{$pet->id}_0.jpg";
        $expectedPath2 = "images/{$today}/{$pet->id}_1.jpg";

        /* Act */
        $pet->storeImages([$fakeImage1, $fakeImage2]);

        /* Assert */
        $this->assertDatabaseHas('pet_images', [
            'pet_id' => $pet->id,
            'path' => $expectedPath1
        ]);

        Storage::disk('public')->assertExists($expectedPath1);
        Storage::disk('public')->assertExists($expectedPath2);

        $actualPaths = $pet->images()->pluck('path')->toArray();
        $this->assertEquals([$expectedPath1, $expectedPath2], $actualPaths);
    }

    public function test_store_detail_to_pet() {
        /* Arrange */
        // Create a pet instance uisng factory
        $pet = Pet::factory()->create();

        // Prepare pet detail data
        $detailData = [
            'adoption_description' => 'test_adoption_description',
            'health_description' => 'test_health_description',
            'adoption_condition' => 'test_adoption_condition'
        ];

        /* Act */
        // Call the method under test
        $pet->storeDetail($detailData);

        /* Assert */
        // Database has details records
        $this->assertDatabaseHas('pet_details', [
            'pet_id' => $pet->id,
            'adoption_description' => 'test_adoption_description'
        ]);

        // Check if the pet's detail matches the stored data
        $this->assertEquals($detailData, $pet->detail->only([
            'adoption_description',
            'health_description',
            'adoption_condition'
        ]));
    }
}
