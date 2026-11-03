<?php

use App\Http\Controllers\PublicLandingPageController;
use App\Http\Controllers\PublicLeaderboardController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\PublicSubmissionController;
use App\Http\Controllers\PublicTicketController;
use Illuminate\Support\Facades\Route;

// Beranda & Landing Page Khusus Bhinneka Tunggal Ika
Route::get('/', [PublicLandingPageController::class, 'home'])->name('home');
Route::get('/bhinneka', [PublicLandingPageController::class, 'bhinneka'])->name('landing.bhinneka');
Route::get('/privacy-policy', [PublicLandingPageController::class, 'privacy'])->name('public.privacy');
Route::get('/terms-and-conditions', [PublicLandingPageController::class, 'terms'])->name('public.terms');
Route::get('/contact', [PublicLandingPageController::class, 'contact'])->name('public.contact');

// Pendaftaran Lomba Lari & Checkout Tripay (Multi-Step Wizard ala Jelatix)
Route::get('/events/{slug}/register', [PublicRegistrationController::class, 'show'])->name('public.register.show');
Route::get('/event/{slug}', [PublicRegistrationController::class, 'show'])->name('public.event.show');
Route::match(['get', 'post'], '/events/{slug}/participants', [PublicRegistrationController::class, 'showParticipants'])->name('public.register.participants');
Route::post('/events/{slug}/checkout', [PublicRegistrationController::class, 'checkout'])->name('public.register.checkout');
Route::get('/orders/{order_code}', [PublicRegistrationController::class, 'showInvoice'])->name('public.order.show');
Route::get('/orders/{order_code}/status', [PublicRegistrationController::class, 'checkStatus'])->name('public.order.status');
Route::post('/orders/{order_code}/simulate', [PublicRegistrationController::class, 'simulatePay'])->name('public.order.simulate');
Route::post('/orders/{order_code}/resend-ticket', [PublicRegistrationController::class, 'resendTicket'])->name('public.order.resend_ticket');

// Fitur Mandiri Pelari: Cek Tiket & Unduh E-Ticket
Route::get('/cek-tiket', [PublicTicketController::class, 'index'])->name('public.ticket.index');
Route::post('/cek-tiket', [PublicTicketController::class, 'search'])->name('public.ticket.search');
Route::get('/ticket/{token}/download', [PublicTicketController::class, 'downloadPdf'])->name('public.ticket.download');

// Universal Submission Portal Virtual Run (Zero-Login / Cukup Nomor E-BIB)
Route::get('/submit', [PublicSubmissionController::class, 'index'])->name('public.submit.index');
Route::post('/submit', [PublicSubmissionController::class, 'store'])->name('public.submit.store');
Route::get('/certificate/{bib}/download', [PublicSubmissionController::class, 'downloadCertificate'])->name('public.certificate.download');

// Live Leaderboard Publik Virtual Run
Route::get('/events/{slug}/leaderboard', [PublicLeaderboardController::class, 'show'])->name('public.leaderboard.show');
