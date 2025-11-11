<?php

use Illuminate\Support\Facades\Route;

// Debug route to check city context
Route::get('/debug-city', function() {
    return response()->json([
        'session' => [
            'selected_city' => session('selected_city'),
            'selected_city_name' => session('selected_city_name'),
            'selected_city_id' => session('selected_city_id'),
            'city_selection_skipped' => session('city_selection_skipped'),
        ],
        'cityContext' => request()->get('cityContext'),
        'all_session' => session()->all(),
        'cities_count' => \App\Models\City::count(),
        'active_cities_count' => \App\Models\City::where('is_active', true)->count(),
    ]);
})->middleware(['city.context']);

// Test set city endpoint
Route::post('/debug-set-city', function() {
    $request = request();
    
    // Validate city slug
    $citySlug = $request->input('city_slug');
    
    if (!$citySlug) {
        return response()->json(['error' => 'city_slug is required'], 400);
    }
    
    $city = \App\Models\City::where('slug', $citySlug)->first();
    
    if (!$city) {
        return response()->json(['error' => 'City not found'], 404);
    }
    
    // Set session
    session(['selected_city' => $city->slug]);
    session(['selected_city_name' => $city->name]);
    session(['selected_city_id' => $city->id]);
    
    return response()->json([
        'success' => true,
        'message' => "City {$city->name} selected successfully",
        'city' => [
            'name' => $city->name,
            'slug' => $city->slug,
            'id' => $city->id
        ],
        'session_after' => [
            'selected_city' => session('selected_city'),
            'selected_city_name' => session('selected_city_name'),
            'selected_city_id' => session('selected_city_id'),
        ]
    ]);
})->middleware(['web']);