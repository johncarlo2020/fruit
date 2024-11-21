<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leaderboard;
use App\Models\Venue;

class LeaderboardController extends Controller
{
    public function save(Request $request)
    {

        // Get today's date
        $today = now()->toDateString();

        // Find the existing user on the leaderboard for today

        // If the user doesn't exist, create a new leaderboard entry for today
        Leaderboard::create([
            'name' => $request['currentUser']['name'],
            'code' => $request['currentUser']['id'],
            'email' => $request['currentUser']['email'],
            'phone' => substr($request['currentUser']['phone'], -4),
            'score' => $request['currentUser']['score'],
            'venue_id' => $request['locationId'], // Assuming 'venue_id' corresponds to 'location_id'
            'date' => $today, // Save the current date
        ]);


        // Fetch the top 5 leaderboard entries for today, sorted by score in descending order
        $leaderboard = Leaderboard::select(
            'code',
            Leaderboard::raw('MAX(name) as name'), // Include name
            Leaderboard::raw('MAX(email) as email'), // Include email
            Leaderboard::raw('MAX(phone) as phone'), // Include phone
            Leaderboard::raw('MAX(venue_id) as venue_id'), // Include venue_id
            Leaderboard::raw('MAX(updated_at) as updated_at'), // Select the most recent update
            Leaderboard::raw('MAX(score) as highest_score') // Select the highest score
        )
            ->where('date', $today)
            ->where('venue_id', $request['locationId']) // Filter by venue_id
            ->groupBy('code') // Group only by code
            ->orderBy('highest_score', 'desc') // Order by highest score
            ->take(5) // Limit to top 5
            ->get();



        // Return the updated leaderboard as JSON
        return response()->json($leaderboard);
    }


    public function list(Request $request)
    {
        // Get today's date
        // Get the list of venues
        $venues = Venue::all();

        // Get the selected date from the request, or default to today's date
        $selectedDate = $request->get('date', now()->toDateString());

        // Query the top 5 leaderboards for each venue, filtered by the selected date
        $leaderboards = [];
        foreach ($venues as $venue) {

            $leaderboardData = Leaderboard::select(
                'code',
                Leaderboard::raw('MAX(name) as name'), // Include name
                Leaderboard::raw('MAX(email) as email'), // Include email
                Leaderboard::raw('MAX(phone) as phone'), // Include phone
                Leaderboard::raw('MAX(venue_id) as venue_id'), // Include venue_id
                Leaderboard::raw('MAX(updated_at) as updated_at'), // Select the most recent update
                Leaderboard::raw('MAX(score) as score') // Select the highest score
            )
                ->whereDate('date', $selectedDate)
                ->where('venue_id', $venue->id) // Filter by venue_id
                ->groupBy('code') // Group only by code
                ->orderBy('score', 'desc') // Order by highest score
                ->take(5) // Limit to top 5
                ->get();

            $userCount = Leaderboard::where('venue_id', $venue->id)
                ->whereDate('date', $selectedDate)
                ->distinct('code')
                ->count('code');
            $gameCount = Leaderboard::where('venue_id', $venue->id)
                ->whereDate('date', $selectedDate)
                ->count('code');

            $leaderboards[$venue->id] = [
                'top_leaderboard' => $leaderboardData,
                'total_users' => $userCount,
                'total_games' => $gameCount,

            ];
        }

        //dd($leaderboards);


        return view('leaderboard', compact('venues', 'leaderboards', 'selectedDate'));
    }

    public function location()
    {
        $locations = Venue::get();
        return view('location', compact('locations'));
    }
}
