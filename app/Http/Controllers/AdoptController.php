<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use Illuminate\Support\Facades\Log;

class AdoptController extends Controller
{
    public function add(Request $request) {
        $types = ['preview' => $request->image['previewList'], 'original' => $request->image['originalList']];
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

            foreach ($types as $type => $list) {
                foreach ($list as $index => $path)
                    $pet->images()->create([
                        'type' => $type,
                        'index' => $index,
                        'path' => $path ?: null
                    ]);
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
