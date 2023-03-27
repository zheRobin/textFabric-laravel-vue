<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class GoogleRecaptcha implements ValidationRule
{
    /**
     * @var string
     */
    private string $verifyUrl;

    /**
     * @var string
     */
    private string $secret;

    /**
     * @var float
     */
    private float $minScore;

    public function __construct()
    {
        $this->verifyUrl = config('services.google_recaptcha.url');
        $this->secret = config('services.google_recaptcha.secret_key');
        $this->minScore = config('services.google_recaptcha.min_score');
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = Http::asForm()->post($this->verifyUrl, [
            'secret' => $this->secret,
            'response' => $value,
            'ip' => request()->ip()
        ]);

        if ($response->failed() ||
            !$response->json('success') ||
            $response->json('score') < $this->minScore) {
            $fail('ReCaptcha verification failed, try again.');
        }
    }
}
