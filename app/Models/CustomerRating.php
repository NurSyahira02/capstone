<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRating extends Model
{
    // Tells Laravel to connect to your specific XAMPP table
    protected $table = 'customer_ratings';

    // Allows these columns to accept your data safely
    protected $fillable = [
        'rating_punctuality',
        'rating_condition',
        'rating_attitude',
        'preferred_courier',
        'choice_reason',
        'rating_trust'
    ];
}