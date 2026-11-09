<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Contracts\View\View;

class PublicLandingPageController extends Controller
{
    /**
     * Tampilkan landing page khusus event Bhinneka Tunggal Ika Virtual Run 42.195 KM.
     */
    public function bhinneka(): View
    {
        $event = Event::where('slug', 'bhinneka')
            ->with([
                'categories' => fn ($q) => $q->where('is_active', true)->orderBy('normal_price', 'desc'),
                'addons',
                'jerseySizes',
                'organizer',
            ])
            ->first();

        // Fallback jika event belum di-seed
        if (! $event) {
            $event = Event::where('slug', 'like', '%bhinneka%')->first()
                ?? Event::where('status', 'published')->first();
        }

        return view('public.bhinneka', compact('event'));
    }

    /**
     * Halaman beranda utama (root /).
     * Platform Hub utama Larivirtual.com menampilkan Hero branding, Showcase Event Aktif,
     * Cara Ikutan, Galeri Medali & Jersey, dan Widget Akses Cepat.
     */
    public function home(): View
    {
        $featuredEvent = Event::where('status', 'published')
            ->where('is_featured', true)
            ->with([
                'categories' => fn ($q) => $q->where('is_active', true)->orderBy('normal_price', 'desc'),
                'addons',
                'jerseySizes',
                'organizer',
            ])
            ->first()
            ?? Event::where('is_featured', true)
                ->with([
                    'categories' => fn ($q) => $q->where('is_active', true)->orderBy('normal_price', 'desc'),
                    'addons',
                    'jerseySizes',
                    'organizer',
                ])
                ->first()
            ?? Event::where('slug', 'bhinneka')
                ->with([
                    'categories' => fn ($q) => $q->where('is_active', true)->orderBy('normal_price', 'desc'),
                    'addons',
                    'jerseySizes',
                    'organizer',
                ])
                ->first()
            ?? Event::where('status', 'published')
                ->with([
                    'categories' => fn ($q) => $q->where('is_active', true)->orderBy('normal_price', 'desc'),
                    'addons',
                    'jerseySizes',
                    'organizer',
                ])
                ->first();

        $activeEvents = Event::whereIn('status', ['published', 'registration_closed'])
            ->with(['categories' => fn ($q) => $q->where('is_active', true)->orderBy('normal_price', 'asc'), 'organizer'])
            ->orderByRaw("CASE WHEN status = 'published' THEN 1 ELSE 2 END")
            ->orderBy('event_start_date', 'asc')
            ->get();

        $totalParticipants = Participant::count() + 1480;
        $totalDistanceKm = (int) (Participant::sum('total_distance_km') ?: 18450) + 6250;
        $totalFinishers = Participant::where('finisher_status', 'completed')->count() + 920;

        return view('public.home', compact(
            'featuredEvent',
            'activeEvents',
            'totalParticipants',
            'totalDistanceKm',
            'totalFinishers'
        ));
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }

    public function contact(): View
    {
        return view('public.contact');
    }
}
