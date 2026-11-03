<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
     * Saat ini default menampilkan Landing Page Bhinneka.
     * Jika kelak ingin diganti ke daftar event atau event lain, cukup atur FEATURED_EVENT_SLUG di .env / config.
     */
    public function home(): View
    {
        $featuredSlug = config('app.featured_event', env('FEATURED_EVENT_SLUG', 'bhinneka'));

        if ($featuredSlug === 'bhinneka') {
            return $this->bhinneka();
        }

        if ($featuredSlug && $featuredSlug !== 'all') {
            $event = Event::where('slug', $featuredSlug)->first();
            if ($event) {
                return view('public.bhinneka', compact('event'));
            }
        }

        // Fallback ke daftar event biasa jika featured_event diset 'all' atau null
        $events = Event::where('status', 'published')
            ->with(['categories', 'organizer'])
            ->orderBy('event_start_date', 'asc')
            ->get();

        return view('public.home', compact('events'));
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
