<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\AdoptionCase;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdoptionCaseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_finalize_adoption_and_create_case()
    {
        $owner = User::factory()->create();
        $adopter = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $owner->id, 'status' => Pet::STATUS_AVAILABLE]);

        $application = AdoptionApplication::create([
            'user_id' => $adopter->id,
            'pet_id' => $pet->id,
            'name' => 'Test Adopter',
            'phone' => '0912345678',
            'housing_type' => 'apartment',
            'experience' => 'experienced',
            'family_agreement' => true,
            'message' => 'Test message',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($owner)->postJson('/api/adoption-cases', [
            'pet_id' => $pet->id,
            'adopter_id' => $adopter->id,
            'application_id' => $application->id,
            'tracking_config' => ['frequency' => 'monthly']
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('adoption_cases', [
            'pet_id' => $pet->id,
            'adopter_id' => $adopter->id,
            'owner_id' => $owner->id,
            'application_id' => $application->id,
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'status' => Pet::STATUS_ADOPTED
        ]);

        $this->assertDatabaseHas('adoption_applications', [
            'id' => $application->id,
            'status' => 'approved'
        ]);
    }

    public function test_non_owner_cannot_finalize_adoption()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $adopter = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->postJson('/api/adoption-cases', [
            'pet_id' => $pet->id,
            'adopter_id' => $adopter->id,
        ]);

        $response->assertStatus(403);
    }
}
