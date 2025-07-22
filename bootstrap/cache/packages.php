<?php return array (
  'barryvdh/laravel-dompdf' => 
  array (
    'aliases' => 
    array (
      'PDF' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
      'Pdf' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
    ),
    'providers' => 
    array (
      0 => 'Barryvdh\\DomPDF\\ServiceProvider',
    ),
  ),
  'darkaonline/l5-swagger' => 
  array (
    'aliases' => 
    array (
      'L5Swagger' => 'L5Swagger\\L5SwaggerFacade',
    ),
    'providers' => 
    array (
      0 => 'L5Swagger\\L5SwaggerServiceProvider',
    ),
  ),
  'fedeisas/laravel-mail-css-inliner' => 
  array (
    'providers' => 
    array (
      0 => 'Fedeisas\\LaravelMailCssInliner\\LaravelMailCssInlinerServiceProvider',
    ),
  ),
  'laravel/octane' => 
  array (
    'aliases' => 
    array (
      'Octane' => 'Laravel\\Octane\\Facades\\Octane',
    ),
    'providers' => 
    array (
      0 => 'Laravel\\Octane\\OctaneServiceProvider',
    ),
  ),
  'laravel/sail' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Sail\\SailServiceProvider',
    ),
  ),
  'laravel/sanctum' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Sanctum\\SanctumServiceProvider',
    ),
  ),
  'laravel/telescope' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Telescope\\TelescopeServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'mailersend/laravel-driver' => 
  array (
    'aliases' => 
    array (
      'LaravelDriver' => 'MailerSend\\LaravelDriver\\LaravelDriverFacade',
    ),
    'providers' => 
    array (
      0 => 'MailerSend\\LaravelDriver\\LaravelDriverServiceProvider',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'nunomaduro/termwind' => 
  array (
    'providers' => 
    array (
      0 => 'Termwind\\Laravel\\TermwindServiceProvider',
    ),
  ),
  'sentry/sentry-laravel' => 
  array (
    'aliases' => 
    array (
      'Sentry' => 'Sentry\\Laravel\\Facade',
    ),
    'providers' => 
    array (
      0 => 'Sentry\\Laravel\\ServiceProvider',
      1 => 'Sentry\\Laravel\\Tracing\\ServiceProvider',
    ),
  ),
  'spatie/laravel-permission' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Permission\\PermissionServiceProvider',
    ),
  ),
  'stancl/tenancy' => 
  array (
    'aliases' => 
    array (
      'Tenancy' => 'Stancl\\Tenancy\\Facades\\Tenancy',
      'GlobalCache' => 'Stancl\\Tenancy\\Facades\\GlobalCache',
    ),
    'providers' => 
    array (
      0 => 'Stancl\\Tenancy\\TenancyServiceProvider',
    ),
  ),
);