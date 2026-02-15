<?php

if (! function_exists('format_money')) {
    /**
     * Formate un montant numérique au format standard de la plateforme.
     *
     * @param  mixed  $amount
     * @param  string  $suffix
     * @return string
     */
    function format_money($amount, $suffix = 'FCFA')
    {
        $value = is_numeric($amount) ? $amount : 0;

        return number_format($value, 0, ',', ' ').' '.$suffix;
    }
}

if (! function_exists('fcfa')) {
    /**
     * Alias pour format_money avec suffixe FCFA par défaut.
     */
    function fcfa($amount)
    {
        return format_money($amount);
    }
}

if (! function_exists('get_secure_url')) {
    /**
     * Génère une URL signée temporaire pour un document.
     *
     * @param  string|null  $path
     * @param  int  $minutes
     * @return string|null
     */
    function get_secure_url($path, $minutes = 60)
    {
        if (! $path) {
            return null;
        }

        return URL::temporarySignedRoute(
            'documents.secure',
            now()->addMinutes($minutes),
            ['path' => encrypt(str_replace('\\', '/', $path))]
        );
    }
}
