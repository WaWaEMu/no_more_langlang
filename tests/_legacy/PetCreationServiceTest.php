<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\PetCreationService;
use App\Services\SendableCityService;
use App\Services\PetImageService;
use App\Services\PetDetailsService;
use App\Models\Pet;
use Mockery;
use Illuminate\Support\Arr;

class PetCreationServiceTest extends TestCase
{
    protected function tearDown(): void {
        Mockery::close();
    }

    public function test_create_pet_succcessfully(): void {
        // Arrange: Prepare test data and mock objects
        $data = [
            'title' => 'test_title',
            'city' => '台北市',
            'town' => '中正區',
            'is_stray' => 0,
            'type' => '狗狗',
            'color' => '白色',
            'fur_type' => '短毛',
            'name' => 'test_name',
            'gender' => 'male',
            'age' => '16+',
            'is_neuter' => 0,
            'sendable_city' => ['台北市', '新北市', '基隆市'],
            'adoption_description' => 'test_description',
            'health_description' => 'test_health_description',
            'adoption_condition' => 'test_condition'
        ];

        $details = Arr::only($data, [
            'adoption_description',
            'health_description',
            'adoption_condition',
        ]);

        $petMock = Mockery::mock(Pet::class);
        $createdPet = new Pet($data);

        $petMock->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($createdPet);

        $cityServiceMock = Mockery::mock(SendableCityService::class);
        $cityServiceMock->shouldReceive('attachToPet')
            ->once()
            ->with($createdPet, $data['sendable_city']);

        $imageServiceMock = Mockery::mock(PetImageService::class);
        $imageServiceMock->shouldReceive('storeImage')
            ->once()
            ->with($createdPet, []);

        $detailServiceMock = Mockery::mock(PetDetailsService::class);
        $detailServiceMock->shouldReceive('storeDetail')
            ->once()
            ->with($createdPet, $details);

        // Act: Excute the method under test
        $service = new PetCreationService($cityServiceMock, $imageServiceMock, $petMock, $detailServiceMock);
        $result = $service->create($data, []);

        // Assert: Verify the result is as expected
        $this->assertInstanceOf(Pet::class, $result);
        $this->assertEquals($data['title'], $result->title);
    }
}
