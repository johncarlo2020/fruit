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
        $user = Leaderboard::where('code', $request['currentUser']['id'])
            ->where('venue_id', $request['locationId'])
            ->where('date', $today) // Ensure the record is for today
            ->first();

        if ($user) {
            // Check if the current score is higher than the previous score for today
            if ($request['currentUser']['score'] > $user->score) {
                // Update the user with the new score
                $user->update([
                    'name' => $request['currentUser']['name'],
                    'code' => $request['currentUser']['id'],
                    'email' => $request['currentUser']['email'],
                    'phone' => substr($request['currentUser']['phone'], -4),
                    'score' => $request['currentUser']['score'],
                    'venue_id' => $request['locationId'], // Assuming 'venue_id' corresponds to 'location_id'
                ]);
            }
        } else {
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
        }

        // Fetch the top 5 leaderboard entries for today, sorted by score in descending order
        $leaderboard = Leaderboard::where('date', $today)
            ->where('venue_id', $request['locationId']) // Filter by location_id
            ->orderBy('score', 'desc')
            ->take(5)
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
            $leaderboards[$venue->id] = Leaderboard::where('venue_id', $venue->id)
                ->whereDate('date', $selectedDate)
                ->orderByDesc('score')
                ->take(5)
                ->get();
        }

        return view('leaderboard', compact('venues', 'leaderboards', 'selectedDate'));
    }

    public function location()
    {
        $locations = Venue::get();
        return view('location', compact('locations'));
    }
}
