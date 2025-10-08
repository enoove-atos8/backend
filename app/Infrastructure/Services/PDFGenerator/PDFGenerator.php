<?php

namespace App\Infrastructure\Services\PDFGenerator;

use Spatie\Browsershot\Browsershot;
class PDFGenerator
{
    private static string $chromePath;
    private static string $nodeBinary;
    private static string $npmBinary;
    private static array $args;
    private static string $format;
    private static bool $showBackground;

    private static function initializeConfig(): void
    {
        self::$chromePath = config('pdf-generator.chrome_path');
        self::$nodeBinary = config('pdf-generator.node_binary');
        self::$npmBinary = config('pdf-generator.npm_binary');
        self::$args = config('pdf-generator.args');
        self::$format = config('pdf-generator.format');
        self::$showBackground = config('pdf-generator.show_background');
    }

    public static function save(string $html, string $path): void
    {
        self::initializeConfig();

        Browsershot::html($html)
            ->setChromePath(self::$chromePath)
            ->setNodeBinary(self::$nodeBinary)
            ->setNpmBinary(self::$npmBinary)
            ->setOption('args', self::$args)
            ->format(self::$format)
            ->showBackground(self::$showBackground)
            ->margins(10, 10, 10, 10)
            ->waitUntilNetworkIdle()
            ->emulateMedia('print')
            ->setOption('printBackground', true)
            ->timeout(180)
            ->setDelay(500)
            ->save($path);
    }
}
