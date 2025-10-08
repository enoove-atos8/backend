<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chrome Path
    |--------------------------------------------------------------------------
    |
    | Here you may specify the path to the Chrome/Chromium executable that
    | will be used by Browsershot to generate PDF files. This path may
    | vary depending on your operating system and installation method.
    |
    */

    'chrome_path' => env('PDF_CHROME_PATH', '/usr/bin/google-chrome-stable'),

    /*
    |--------------------------------------------------------------------------
    | Node Binary
    |--------------------------------------------------------------------------
    |
    | This value determines the path to the Node.js binary that will be
    | used by Browsershot. Make sure Node.js is installed and the path
    | is correctly configured for your environment.
    |
    */

    'node_binary' => env('PDF_NODE_BINARY', '/usr/bin/node'),

    /*
    |--------------------------------------------------------------------------
    | NPM Binary
    |--------------------------------------------------------------------------
    |
    | This value determines the path to the NPM binary that will be used
    | by Browsershot. Ensure NPM is installed and accessible at the
    | specified path for proper PDF generation functionality.
    |
    */

    'npm_binary' => env('PDF_NPM_BINARY', '/usr/bin/npm'),

    /*
    |--------------------------------------------------------------------------
    | Chrome Arguments
    |--------------------------------------------------------------------------
    |
    | These are the arguments that will be passed to the Chrome process when
    | generating PDFs. The default arguments disable the sandbox for better
    | compatibility in containerized environments like Docker.
    |
    */

    'args' => ['--no-sandbox', '--disable-setuid-sandbox'],

    /*
    |--------------------------------------------------------------------------
    | PDF Format
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default paper format for generated PDF files.
    | Common formats include: A4, Letter, Legal, Tabloid, Ledger, A0-A6, etc.
    |
    */

    'format' => env('PDF_FORMAT', 'A4'),

    /*
    |--------------------------------------------------------------------------
    | Show Background
    |--------------------------------------------------------------------------
    |
    | This option determines whether background graphics and colors should be
    | included in the generated PDF. Set to true to include backgrounds or
    | false to exclude them for a cleaner, printer-friendly output.
    |
    */

    'show_background' => env('PDF_SHOW_BACKGROUND', true),

];
