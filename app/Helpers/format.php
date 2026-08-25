<?php

if (! function_exists('format_money')) {
    function format_money(float|int|string|null $amount, ?string $currency = null): string
    {
        $currency ??= \App\Models\ClinicSetting::current()->currency;

        return number_format((float) $amount, 0, ',', ' ').' '.$currency;
    }
}
