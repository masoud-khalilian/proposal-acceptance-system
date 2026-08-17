<?php

declare(strict_types=1);

namespace App\Support;

final class Translator
{
    private const SUPPORTED_LOCALES = ['fa', 'en'];
    private const DEFAULT_LOCALE = 'fa';

    /** @var array<string, array<string, string>> */
    private array $strings = [];

    private string $locale;

    public function __construct(private readonly string $translationsPath, ?string $locale = null)
    {
        $this->locale = $this->normalize($locale);
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $this->normalize($locale);
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function direction(): string
    {
        return $this->locale === 'fa' ? 'rtl' : 'ltr';
    }

    public function translate(string $key, array $params = []): string
    {
        $strings = $this->loadLocale($this->locale);
        $text = $strings[$key] ?? $key;

        foreach ($params as $name => $value) {
            $text = str_replace(':' . $name, (string) $value, $text);
        }

        return $text;
    }

    private function normalize(?string $locale): string
    {
        return in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : self::DEFAULT_LOCALE;
    }

    /** @return array<string, string> */
    private function loadLocale(string $locale): array
    {
        if (!isset($this->strings[$locale])) {
            $file = $this->translationsPath . '/' . $locale . '.php';
            $this->strings[$locale] = is_file($file) ? require $file : [];
        }

        return $this->strings[$locale];
    }
}
