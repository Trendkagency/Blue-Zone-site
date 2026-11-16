<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Security hardening added 2026-11-16 after the 2026-11-15 webshell incident.
 * Blocks executable/script uploads (php, phtml, phar, shtml, html, js, svg, ...)
 * by checking BOTH the client-provided extension and the content-sniffed one.
 */
class SafeUpload
{
    public const IMAGES = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

    public const DOCUMENTS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'heic', 'heif',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'ppt', 'pptx', 'odt', 'ods',
        'zip', 'rar', '7z', 'mp4', 'mov', 'mp3', 'm4a', 'wav',
    ];

    private const DANGEROUS = '/\.(ph(p\d?|tml|ar|ps|t)|s?html?|xhtml|shtm|stm|js|mjs|svgz?|cgi|pl|py|rb|sh|bash|exe|bat|cmd|asp|aspx|jsp|htaccess|user\.ini)(\.|$)/i';

    /** Validate an image upload and return a safe lowercase extension. */
    public static function image(UploadedFile $file, string $field = 'file'): string
    {
        return self::check($file, self::IMAGES, $field, true);
    }

    /** Validate a document/attachment upload and return the same file (chainable). */
    public static function file(UploadedFile $file, string $field = 'file'): UploadedFile
    {
        self::check($file, self::DOCUMENTS, $field, false);

        return $file;
    }

    /** A safe file name: slugged base name + validated extension. */
    public static function safeName(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';

        return Str::limit($base, 80, '') . '_' . Str::random(6) . '.' . $ext;
    }

    private static function check(UploadedFile $file, array $allowed, string $field, bool $strictGuess): string
    {
        $name = (string) $file->getClientOriginalName();
        $client = strtolower($file->getClientOriginalExtension());
        $guessed = strtolower((string) $file->guessExtension());

        $fail = fn () => throw ValidationException::withMessages([
            $field => app()->getLocale() === 'ar'
                ? 'نوع الملف غير مسموح.'
                : 'This file type is not allowed.',
        ]);

        if ($client === '' || ! in_array($client, $allowed, true) || preg_match(self::DANGEROUS, $name)) {
            $fail();
        }
        if ($guessed !== '' && preg_match(self::DANGEROUS, '.' . $guessed)) {
            $fail();
        }
        if ($strictGuess && $guessed !== '' && ! in_array($guessed, $allowed, true)) {
            $fail();
        }

        return $client;
    }
}
