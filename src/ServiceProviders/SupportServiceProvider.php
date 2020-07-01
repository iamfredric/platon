<?php

namespace Platon\ServiceProviders;

use Illuminate\Support\Str;
use Platon\Application;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     */
    public function boot(Application $app)
    {
        $app->booted(function () {
            if (! Str::hasMacro('tel')) {
                Str::macro('tel', function ($number, $countryCode = '46') {
                    $number = str_replace('(0)', '', $number);

                    $number = preg_replace('/[^+0-9]+/', '', $number);

                    if (substr($number, 0, 2) == '00') {
                        $number = '+' . substr($number, 2);
                    }

                    if (substr($number, 0, 1) != '+') {
                        $number = "+{$countryCode}" . substr($number, 1);
                    }

                    return $number;
                });
            }
        });
    }
}
