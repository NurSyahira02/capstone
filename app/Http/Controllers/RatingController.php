<?php

namespace App\Http\Controllers;

use App\Models\CustomerRating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        // 1. Calculate the average scores from your 52 survey respondents
        $avgPunctuality = CustomerRating::avg('rating_punctuality') ?? 0;
        $avgCondition = CustomerRating::avg('rating_condition') ?? 0;
        $avgAttitude = CustomerRating::avg('rating_attitude') ?? 0;
        $avgTrust = CustomerRating::avg('rating_trust') ?? 0;

        // 2. Count total survey responses (Should equal 52)
        $totalResponses = CustomerRating::count();

        // 3. Send these calculations to the view
        // (We will make sure whichever view we choose can read these variables perfectly!)
        return view('dashboard', compact(
            'avgPunctuality', 
            'avgCondition', 
            'avgAttitude', 
            'avgTrust', 
            'totalResponses'
        ));
    }
}