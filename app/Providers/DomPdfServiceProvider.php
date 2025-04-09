<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dompdf\Dompdf;
use Dompdf\Options;

class DomPdfServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('dompdf', function ($app) {
            $options = new Options();
            
            // Check if GD extension is loaded
            $hasGd = extension_loaded('gd');
            
            // Configure options
            $options->setDefaultFont('DejaVu Sans');
            $options->setIsHtml5ParserEnabled(true);
            $options->setIsRemoteEnabled(true);
            $options->setIsFontSubsettingEnabled(false);
            
            // Set image rendering options based on GD availability
            if (!$hasGd) {
                $options->setIsPhpEnabled(false);
                // Use fallback for image processing
                $options->setChroot(realpath(base_path()));
            }
            
            $dompdf = new Dompdf($options);
            $dompdf->setBasePath(public_path());
            
            return $dompdf;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 