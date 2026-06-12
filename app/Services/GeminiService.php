<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $models = [
        'gemini-2.5-flash-lite',
        'gemini-flash-lite-latest',
        'gemini-2.5-flash'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Generate description for a medicine based on its name and optionally category.
     *
     * @param string $medicineName
     * @param string|null $categoryName
     * @return string
     * @throws \Exception
     */
    public function generateDescription(string $medicineName, ?string $categoryName = null): string
    {
        if (!$this->apiKey) {
            throw new \Exception('API Key Gemini belum diset. Silakan tambahkan GEMINI_API_KEY di file .env Anda.');
        }

        $prompt = "Tuliskan deskripsi yang sangat singkat, jelas, dan mudah dipahami orang awam untuk obat bernama \"{$medicineName}\"";
        if ($categoryName) {
            $prompt .= " (kategori: \"{$categoryName}\")";
        }
        $prompt .= ". Deskripsi ini akan dibaca oleh pembeli di apotek online. ";
        $prompt .= "Berikan penjelasan yang sangat praktis tentang kegunaan obat ini dan gejalanya dengan bahasa sehari-hari. Hindari istilah medis yang rumit. ";
        $prompt .= "Batasi panjang tulisan maksimal hanya 2 kalimat pendek saja. Jangan menggunakan format markdown (seperti bold, bullet points, atau tanda asterisk), berikan teks mentah paragraf biasa.";

        $lastException = null;

        foreach ($this->models as $model) {
            try {
                $response = Http::withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

                if ($response->failed()) {
                    Log::warning("Gemini API warning for model {$model}: " . $response->body());
                    $lastException = new \Exception('Gagal menghubungi Gemini API: ' . ($response->json('error.message') ?? 'Terjadi kesalahan sistem.'));
                    continue; // try next model
                }

                $text = $response->json('candidates.0.content.parts.0.text');

                if (!$text) {
                    Log::warning("Gemini API response empty for model {$model}: " . $response->body());
                    $lastException = new \Exception('Hasil generate deskripsi kosong.');
                    continue; // try next model
                }

                return trim($text);

            } catch (\Exception $e) {
                Log::warning("Gemini Service Exception for model {$model}: " . $e->getMessage());
                $lastException = $e;
                continue; // try next model
            }
        }

        Log::error('Gemini Service: All models failed.');
        throw $lastException ?? new \Exception('Gagal generate deskripsi menggunakan AI.');
    }
}
