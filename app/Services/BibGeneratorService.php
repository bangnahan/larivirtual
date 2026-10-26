<?php

namespace App\Services;

use App\Models\EventCategory;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class BibGeneratorService
{
    /**
     * Generate nomor BIB otomatis untuk peserta
     * - Jika kategori memiliki bib_prefix: Menggunakan format prefix kategori (misal: 10K1001)
     * - Jika bib_prefix kosong / virtual run: Menggunakan nomor numerik sederhana mulai dari 1001 bertambah global
     */
    public function autoAssignBib(Participant $participant): string
    {
        if (! empty($participant->bib_number)) {
            return $participant->bib_number;
        }

        $category = $participant->category;

        return DB::transaction(function () use ($participant, $category) {
            $prefix = $category?->bib_prefix ?: '';
            $reservedPool = (array) ($category?->reserved_bib_numbers ?: []);

            if (! empty($prefix)) {
                // Mode dengan prefix kategori
                $startNum = (int) ($category->bib_start_number ?: 1001);
                $existingBibs = Participant::where('event_category_id', $category->id)
                    ->whereNotNull('bib_number')
                    ->where('id', '!=', $participant->id)
                    ->pluck('bib_number')
                    ->toArray();

                $candidateNum = $startNum;
                while (true) {
                    $candidateBib = $prefix.$candidateNum;

                    if (! in_array($candidateNum, $reservedPool) &&
                        ! in_array((string) $candidateNum, $reservedPool) &&
                        ! in_array($candidateBib, $existingBibs)) {
                        break;
                    }

                    $candidateNum++;
                }

                $finalBib = $prefix.$candidateNum;
            } else {
                // Mode Virtual Run Sederhana: Numerik murni tanpa prefix (1001, 1002, ...)
                $startNum = (int) ($category?->bib_start_number ?: 1001);
                $maxBib = DB::table('participants')
                    ->whereNotNull('bib_number')
                    ->whereRaw("bib_number GLOB '[0-9]*' AND bib_number NOT LIKE '%-%' AND bib_number NOT LIKE '%K%' AND LENGTH(bib_number) <= 8")
                    ->selectRaw('MAX(CAST(bib_number AS INTEGER)) as max_bib')
                    ->value('max_bib');

                $candidateNum = ($maxBib && (int) $maxBib >= $startNum) ? ((int) $maxBib + 1) : $startNum;

                while (in_array($candidateNum, $reservedPool) ||
                       in_array((string) $candidateNum, $reservedPool) ||
                       DB::table('participants')->where('bib_number', (string) $candidateNum)->exists()) {
                    $candidateNum++;
                }

                $finalBib = (string) $candidateNum;
            }

            $participant->update(['bib_number' => $finalBib]);

            return $finalBib;
        });
    }

    /**
     * Remap / Urutkan ulang nomor BIB massal
     */
    public function bulkRemapBibs(
        EventCategory $category,
        string $sortBy = 'created_at',
        string $direction = 'asc'
    ): int {
        return DB::transaction(function () use ($category, $sortBy, $direction) {
            $prefix = $category->bib_prefix ?: '';
            $startNum = (int) ($category->bib_start_number ?: 1001);
            $reservedPool = (array) ($category->reserved_bib_numbers ?: []);

            $participants = Participant::where('event_category_id', $category->id)
                ->whereHas('order', function ($query) {
                    $query->where('status', 'paid');
                })
                ->orderBy($sortBy, $direction)
                ->get();

            $currentNum = $startNum;
            $count = 0;

            foreach ($participants as $runner) {
                while (true) {
                    $candidateBib = $prefix.$currentNum;

                    if (! in_array($currentNum, $reservedPool) &&
                        ! in_array((string) $currentNum, $reservedPool)) {
                        break;
                    }

                    $currentNum++;
                }

                $runner->update(['bib_number' => $prefix.$currentNum]);
                $currentNum++;
                $count++;
            }

            return $count;
        });
    }
}
