<?php

namespace Application\Core\Helpers;

class PathHelper
{
    public static function getImagePath(string $tenant, string $link): string
    {
        return storage_path('app/tenants/' . $tenant . '/reports/temp/' . $link);
    }
}
