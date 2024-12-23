<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    use HasFactory;

    public $fillable = ['code', 'name', 'phone', 'email', 'score', 'date', 'venue_id'];
}
