<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\MimeTypes;

class StorageFileExtensionRule implements ValidationRule
{
    private array $params;

    public function __construct(...$params)
    {
        $this->params = $params;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file_mime = MimeTypes::getDefault()->guessMimeType(Storage::path($value));
        if ($file_mime === null || in_array($file_mime, $this->params) === false) {
            $fail('Неподходящий тип файла');
        }
    }
}
