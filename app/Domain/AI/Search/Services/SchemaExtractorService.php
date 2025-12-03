<?php

namespace App\Domain\AI\Search\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SchemaExtractorService
{
    private const CACHE_KEY = 'ai_search_schema';

    private const CACHE_TTL_SECONDS = 3600;

    /** @var array<string> */
    private array $allowedTables = [
        'members',
        'entries',
        'exits',
        'cults',
        'accounts',
        'accounts_balances',
        'cards',
        'cards_purchases',
        'cards_invoices',
        'ecclesiastical_divisions_groups',
        'ecclesiastical_divisions',
        'payment_category',
        'payment_item',
        'movements',
        'financial_reviewers',
        'consolidation_entries',
        'users',
        'user_details',
    ];

    public function extract(): string
    {
        $tenantId = tenant('id') ?? 'default';
        $cacheKey = self::CACHE_KEY.'_'.$tenantId;

        return Cache::store('file')->remember($cacheKey, self::CACHE_TTL_SECONDS, function () {
            return $this->buildSchema();
        });
    }

    public function clearCache(): void
    {
        $tenantId = tenant('id') ?? 'default';
        $cacheKey = self::CACHE_KEY.'_'.$tenantId;

        Cache::store('file')->forget($cacheKey);
    }

    private function buildSchema(): string
    {
        $schema = '';

        foreach ($this->allowedTables as $tableName) {
            $tableComment = $this->getTableComment($tableName);
            $columns = $this->getColumnsWithComments($tableName);

            if (empty($columns)) {
                continue;
            }

            if ($tableComment) {
                $schema .= "-- {$tableComment}\n";
            }

            $schema .= "CREATE TABLE {$tableName} (\n";

            $columnDefinitions = [];
            foreach ($columns as $column) {
                $definition = "    {$column->Field} {$column->Type}";

                if ($column->Comment) {
                    $definition .= " COMMENT '{$column->Comment}'";
                }

                $columnDefinitions[] = $definition;
            }

            $schema .= implode(",\n", $columnDefinitions);
            $schema .= "\n);\n\n";
        }

        return $schema;
    }

    private function getTableComment(string $tableName): ?string
    {
        $result = DB::select('
            SELECT TABLE_COMMENT
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
        ', [$tableName]);

        return $result[0]->TABLE_COMMENT ?? null;
    }

    /**
     * @return array<object>
     */
    private function getColumnsWithComments(string $tableName): array
    {
        try {
            return DB::select("SHOW FULL COLUMNS FROM {$tableName}");
        } catch (\Exception $e) {
            return [];
        }
    }
}
