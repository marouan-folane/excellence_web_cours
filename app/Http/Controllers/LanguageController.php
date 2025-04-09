<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    /**
     * Set the application locale based on the URL parameter.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocale($locale)
    {
        if (in_array($locale, config('app.available_locales'))) {
            session()->put('locale', $locale);
            App::setLocale($locale);

            // Store the locale in a cookie that lasts 30 days
            cookie()->queue('locale', $locale, 43200); // 30 days in minutes
            
            // Special handling for Arabic PDF
            if ($locale === 'ar') {
                config(['dompdf.options.isRtl' => true]);
                config(['dompdf.options.defaultFont' => 'DejaVu Sans']);
            } else {
                config(['dompdf.options.isRtl' => false]);
                config(['dompdf.options.defaultFont' => 'DejaVu Sans']);
            }
        }
        
        return redirect()->back();
    }
} 