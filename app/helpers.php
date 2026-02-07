<?php

if (! function_exists('fcfa')) {
    function fcfa($montant)
    {
        return number_format($montant, 0, ',', ' ').' FCFA';
    }
}
