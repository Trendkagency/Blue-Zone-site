<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CaptchaService
{
    protected const SESSION_HASH_KEY = 'bz_captcha_hash';
    protected const SESSION_EXPIRES_KEY = 'bz_captcha_expires_at';
    protected const TTL_MINUTES = 10;

    /**
     * Check if CAPTCHA is enabled for a given context (login, register, forgot-password, admin).
     */
    public static function isEnabled(string $context = 'login'): bool
    {
        // Always enabled by default for bot protection unless explicitly disabled in settings
        $key = match ($context) {
            'register' => 'enable_captcha_register',
            'forgot-password', 'password' => 'enable_captcha_forgot_password',
            'admin', 'admin_login' => 'enable_captcha_admin_login',
            default => 'enable_captcha_login',
        };

        return (bool) Setting::get($key, true);
    }

    /**
     * Generate a new clinical security CAPTCHA challenge and store its hash in session.
     *
     * @return array{question: string, svg: string}
     */
    public static function generate(): array
    {
        // Math challenge for high human readability & zero OCR ambiguity
        $operators = ['+', '-', '×'];
        $op = $operators[array_rand($operators)];

        if ($op === '+') {
            $n1 = random_int(3, 19);
            $n2 = random_int(2, 15);
            $answer = $n1 + $n2;
            $question = "{$n1} + {$n2} = ?";
        } elseif ($op === '-') {
            $n1 = random_int(10, 29);
            $n2 = random_int(2, 9);
            $answer = $n1 - $n2;
            $question = "{$n1} - {$n2} = ?";
        } else {
            $n1 = random_int(2, 8);
            $n2 = random_int(2, 5);
            $answer = $n1 * $n2;
            $question = "{$n1} × {$n2} = ?";
        }

        // Store hashed answer in session
        Session::put(self::SESSION_HASH_KEY, Hash::make((string) $answer));
        Session::put(self::SESSION_EXPIRES_KEY, now()->addMinutes(self::TTL_MINUTES)->timestamp);

        // Build SVG visualization
        $svg = self::renderSvg($question);

        return [
            'question' => $question,
            'svg' => $svg,
        ];
    }

    /**
     * Verify the submitted CAPTCHA answer.
     */
    public static function verify(?string $submittedAnswer): bool
    {
        // Testing bypass for automated test suites
        if (app()->environment('testing')) {
            if ($submittedAnswer === 'bypass' || $submittedAnswer === 'test' || $submittedAnswer === '1234') {
                return true;
            }
        }

        if ($submittedAnswer === null || trim($submittedAnswer) === '') {
            return false;
        }

        $hash = Session::get(self::SESSION_HASH_KEY);
        $expiresAt = Session::get(self::SESSION_EXPIRES_KEY);

        if (!$hash || !$expiresAt || now()->timestamp > $expiresAt) {
            return false;
        }

        $clean = trim((string) $submittedAnswer);
        $isValid = Hash::check($clean, $hash);

        if ($isValid) {
            // Invalidate after single use to prevent replay attacks
            Session::forget([self::SESSION_HASH_KEY, self::SESSION_EXPIRES_KEY]);
        }

        return $isValid;
    }

    /**
     * Render a crisp, clinical vector SVG challenge with interference curves and noise.
     */
    protected static function renderSvg(string $text): string
    {
        $width = 160;
        $height = 46;

        // Generate dynamic noise lines and dots
        $lines = '';
        for ($i = 0; $i < 3; $i++) {
            $x1 = random_int(5, 40);
            $y1 = random_int(5, 40);
            $x2 = random_int(120, 155);
            $y2 = random_int(5, 40);
            $cx = random_int(50, 110);
            $cy = random_int(2, 44);
            $color = $i % 2 === 0 ? '#2A8FC2' : '#67B34A';
            $opacity = random_int(30, 60) / 100;
            $lines .= "<path d=\"M{$x1},{$y1} Q{$cx},{$cy} {$x2},{$y2}\" fill=\"none\" stroke=\"{$color}\" stroke-width=\"1.4\" stroke-opacity=\"{$opacity}\" />";
        }

        $dots = '';
        for ($i = 0; $i < 18; $i++) {
            $dx = random_int(5, 155);
            $dy = random_int(5, 41);
            $r = random_int(1, 2);
            $dots .= "<circle cx=\"{$dx}\" cy=\"{$dy}\" r=\"{$r}\" fill=\"#0A4F78\" fill-opacity=\"0.15\" />";
        }

        // SVG wrapper with dark mode adaptive background and text
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" class="w-full h-full rounded-xl overflow-hidden select-none" style="filter: drop-shadow(0 1px 2px rgba(0,0,0,0.05));">
    <defs>
        <linearGradient id="bz-cap-grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#0A4F78" stop-opacity="0.08" />
            <stop offset="100%" stop-color="#2A8FC2" stop-opacity="0.04" />
        </linearGradient>
    </defs>
    <rect width="100%" height="100%" fill="url(#bz-cap-grad)" rx="10" stroke="#0A4F78" stroke-opacity="0.18" stroke-width="1" />
    {$lines}
    {$dots}
    <text x="50%" y="58%" font-family="ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace" font-size="20" font-weight="900" fill="#0A4F78" class="dark:fill-[#2A8FC2]" letter-spacing="3" text-anchor="middle" dominant-baseline="middle">
        {$text}
    </text>
</svg>
SVG;
    }
}
