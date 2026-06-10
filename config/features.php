<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Here you can enable or disable specific features of the application.
    | These flags are used in Controllers, Views, and Routes.
    |
    */

    // Sanadaltaleb.com Website
    'website_enabled' => env('FEATURE_WEBSITE_ENABLED', true),
    
    // Code package export features
    'code_export_pdf' => env('FEATURE_CODE_EXPORT_PDF', true),
    'code_export_excel' => env('FEATURE_CODE_EXPORT_EXCEL', true),
    
    // Advanced notification system
    'advanced_notifications' => env('FEATURE_ADVANCED_NOTIFICATIONS', true),
    
    // Student devices display page
    'student_devices' => env('FEATURE_STUDENT_DEVICES', true),
    
    // Technical support interface (OTP codes)
    'phone_verification_codes' => env('FEATURE_PHONE_VERIFICATION_CODES', true),
    
    // App updates management system
    'app_updates' => env('FEATURE_APP_UPDATES', true),
];
