<?php

use Illuminate\Support\Facades\App;

if (! function_exists('format_money')) {
    /**
     * Format numeric amount as RON/lei according to locale.
     */
    function format_money(float|int $amount, ?string $currency = 'RON'): string
    {
        $locale = App::getLocale();

        if ($locale === 'ro') {
            // Romanian format: 1.234,56 lei
            return number_format((float) $amount, 2, ',', '.') . ' lei';
        }

        // Default EN: RON 1,234.56
        return ($currency ?: 'RON') . ' ' . number_format((float) $amount, 2, '.', ',');
    }
}


