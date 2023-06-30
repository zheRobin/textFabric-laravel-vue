<?php

namespace Modules\Compilations\Providers;

use Illuminate\Support\ServiceProvider;

class LocalizationProvider extends ServiceProvider
{
    function getLocale()
    {
        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $url);

        return $parts[1];
    }
}
