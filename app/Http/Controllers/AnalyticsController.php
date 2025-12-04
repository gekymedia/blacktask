<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    // app/Http/Controllers/AnalyticsController.php
public function index()
{
    $data = [
        'daily_completion' => auth()->user()->tasks()
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as completed')
            ->where('is_done', true)
            ->groupBy('date')
            ->orderBy('date')
            ->get(),
            
        'category_distribution' => auth()->user()->tasks()
            ->with('category')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->get(),
            
        'productivity' => auth()->user()->tasks()
            ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->get()
    ];
    
    return view('analytics', compact('data'));
}
}
