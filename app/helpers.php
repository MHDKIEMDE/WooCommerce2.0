<?php

/**
 * Formate un montant selon la devise configurée dans les paramètres admin.
 * Paramètres : shop_currency (symbole), currency_position (before|after),
 *              currency_decimals (0|2…), currency_dec_sep, currency_thou_sep
 */
if (! function_exists('fmt_price')) {
    function fmt_price(float $amount): string
    {
        static $settings = null;
        if ($settings === null) {
            $settings = \App\Models\Setting::getGroup('shop');
        }

        $symbol   = $settings['shop_currency']      ?? 'FCFA';
        $position = $settings['currency_position']  ?? 'after';   // 'before' | 'after'
        $decimals = (int) ($settings['currency_decimals']  ?? 0);
        $decSep   = $settings['currency_dec_sep']   ?? ',';
        $thouSep  = $settings['currency_thou_sep']  ?? ' ';

        $formatted = number_format($amount, $decimals, $decSep, $thouSep);

        return $position === 'before'
            ? $symbol . $formatted
            : $formatted . ' ' . $symbol;
    }
}
