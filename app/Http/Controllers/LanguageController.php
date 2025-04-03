<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Set the application locale.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocale($locale)
    {
        if (!in_array($locale, ['en', 'fr', 'ar'])) {
            $locale = 'en';
        }
        
        session(['locale' => $locale]);
        
        return redirect()->back();
    }
} 