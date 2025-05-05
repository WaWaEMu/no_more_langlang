<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use Illuminate\Support\Facades\Log;

class AdoptController extends Controller
{
    public function add(Request $request) {
        $cities = $request->sendableCity;

        try {
            $pet = Pet::create([
                'title' => $request->title,
                'city' => $request->city,
                'town' => $request->town,
                'is_stray' => $request->isStray,
                'type' => $request->type,
                'color' => $request->color,
                'fur_type' => $request->furType,
                'name' => $request->name,
                'gender' => $request->gender,
                'age' => $request->age,
                'is_neuter' => $request->isNeuter,
                'sendable_city' => $request->sendableCity,
                'description' => $request->description,
                'health_description' => $request->healthDescription,
                'condition' => $request->condition
            ]);

            foreach ($cities as $city) {
                $pet->sendableCitites()->create([
                    'city' => $city
                ]);
            }

            $index = 0;
            $date = now()->format('Y_m_d');
            foreach ($request->file('blobs') as $image) {
                $extension = $image->guessExtension();
                $filename = $pet->id . '_' . $index . '.' . $extension;
                $path = $image->storeAs("images/{$date}", $filename, 'public');
                $pet->images()->create([
                    'type' => 'preview',
                    'index' => $index,
                    'path' => $path ?: null
                ]);
                $index++;
            }

            return response()->json([
                'success' => true
            ]);
        } catch(Throwable $error) {
            \Log::error('新增寵物失敗：' . $error->getMessage());

            return response()->json([
                'success' => false
            ]);
        }
    }
}
