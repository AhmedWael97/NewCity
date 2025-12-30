<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TawselaController extends Controller
{
    /**
     * Display a listing of rides
     */
    public function index(): View
    {
        $cities = City::orderBy('name')->get();
        
        return view('tawsela.index', compact('cities'));
    }

    /**
     * Show the form for creating a new ride
     */
    public function create(): View
    {
        // Redirect guests to login
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً لإنشاء رحلة');
        }
        
        $cities = City::orderBy('name')->get();
        
        return view('tawsela.create', compact('cities'));
    }

    /**
     * Display the specified ride
     */
    public function show($id): View
    {
        $cities = City::orderBy('name')->get();
        
        return view('tawsela.show', compact('id', 'cities'));
    }

    /**
     * Show user's rides
     */
    public function myRides(): View
    {
        $this->middleware('auth');
        
        return view('tawsela.my-rides');
    }

    /**
     * Show user's requests
     */
    public function myRequests(): View
    {
        $this->middleware('auth');
        
        return view('tawsela.my-requests');
    }

    /**
     * Show messages/chat
     */
    public function messages(): View
    {
        $this->middleware('auth');
        
        return view('tawsela.messages');
    }
}
