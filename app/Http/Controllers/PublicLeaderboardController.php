<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicLeaderboardController extends Controller
{
    /**
     * Tampilkan Live Leaderboard Publik per Event Virtual
     */
    public function show(string $slug, Request $request): View
    {
        $event = Event::where('slug', $slug)
            ->with(['categories' => fn ($q) => $q->where('is_active', true)])
            ->firstOrFail();

        $categoryId = $request->query('category_id');
        $gender = $request->query('gender');

        $query = Participant::whereHas('category', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })
            ->whereHas('order', function ($q) {
                $q->where('status', 'paid');
            })
            ->with(['category', 'jerseySize']);

        if (! empty($categoryId)) {
            $query->where('event_category_id', $categoryId);
        }

        if (! empty($gender)) {
            $query->where('gender', strtolower($gender));
        }

        // Urutkan: Finisher lebih dulu, jarak terjauh, lalu durasi tercepat
        $participants = $query
            ->orderByRaw("CASE WHEN finisher_status = 'finisher' THEN 0 ELSE 1 END")
            ->orderByDesc('total_distance_km')
            ->orderBy('total_duration_seconds')
            ->paginate(50)
            ->withQueryString();

        return view('public.leaderboard', compact('event', 'participants', 'categoryId', 'gender'));
    }
}
