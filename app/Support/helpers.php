<?php

use Illuminate\Support\Facades\App;

if (! function_exists('format_money')) {
    /**
     * Format numeric amount according to currency and locale.
     */
    function format_money(float|int $amount, ?string $currency = 'RON'): string
    {
        $locale = App::getLocale();
        $currency = $currency ?: 'RON';

        if ($locale === 'ro') {
            // Romanian format: 1.234,56 lei / 1.234,56 €
            $formatted = number_format((float) $amount, 2, ',', '.');
            return match($currency) {
                'EUR' => $formatted . ' €',
                'RON' => $formatted . ' lei',
                default => $formatted . ' ' . $currency,
            };
        }

        // English format: RON 1,234.56 / EUR 1,234.56
        $formatted = number_format((float) $amount, 2, '.', ',');
        return match($currency) {
            'EUR' => 'EUR ' . $formatted,
            'RON' => 'RON ' . $formatted,
            default => $currency . ' ' . $formatted,
        };
    }
}

if (! function_exists('format_currency')) {
    /**
     * Format numeric amount as currency according to locale.
     * Alias for format_money function.
     */
    function format_currency(float|int $amount, ?string $currency = 'RON'): string
    {
        return format_money($amount, $currency);
    }
}


