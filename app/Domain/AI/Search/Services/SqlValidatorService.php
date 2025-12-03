<?php

namespace App\Domain\AI\Search\Services;

class SqlValidatorService
{
    public const ERROR_ONLY_SELECT_ALLOWED = 'Apenas consultas SELECT são permitidas';

    public const ERROR_FORBIDDEN_KEYWORD = 'Palavra-chave não permitida: %s';

    public const ERROR_MULTIPLE_QUERIES = 'Múltiplas queries não são permitidas';

    public const ERROR_TABLE_NOT_ALLOWED = 'Tabela não permitida: %s';

    public const ERROR_COMMENTS_NOT_ALLOWED = 'Comentários SQL não são permitidos';

    /** @var array<string> */
    private array $forbiddenKeywords = [
        'INSERT',
        'UPDATE',
        'DELETE',
        'DROP',
        'ALTER',
        'TRUNCATE',
        'CREATE',
        'REPLACE',
        'GRANT',
        'REVOKE',
        'EXEC',
        'EXECUTE',
        'UNION',
        'INTO OUTFILE',
        'INTO DUMPFILE',
        'LOAD_FILE',
        'BENCHMARK',
        'SLEEP',
        'INFORMATION_SCHEMA',
        'mysql.',
    ];

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
        'users',
        'user_details',
        'consolidation_entries',
    ];

    /**
     * @return array{valid: bool, reason: string|null}
     */
    public function validate(string $sql): array
    {
        $sqlUpper = strtoupper($sql);

        if (! preg_match('/^\s*SELECT\s/i', $sql)) {
            return [
                'valid' => false,
                'reason' => self::ERROR_ONLY_SELECT_ALLOWED,
            ];
        }

        foreach ($this->forbiddenKeywords as $keyword) {
            $pattern = '/\b'.preg_quote($keyword, '/').'\b/i';
            if (preg_match($pattern, $sql)) {
                return [
                    'valid' => false,
                    'reason' => sprintf(self::ERROR_FORBIDDEN_KEYWORD, $keyword),
                ];
            }
        }

        $cleanSql = preg_replace('/\'[^\']*\'/', '', $sql);
        if (substr_count($cleanSql, ';') > 1) {
            return [
                'valid' => false,
                'reason' => self::ERROR_MULTIPLE_QUERIES,
            ];
        }

        preg_match_all('/(?:FROM|JOIN)\s+`?(\w+)`?/i', $sql, $matches);
        $usedTables = array_map('strtolower', $matches[1] ?? []);

        foreach ($usedTables as $table) {
            if (! in_array($table, $this->allowedTables)) {
                return [
                    'valid' => false,
                    'reason' => sprintf(self::ERROR_TABLE_NOT_ALLOWED, $table),
                ];
            }
        }

        if (preg_match('/--|\\/\\*|#/', $sql)) {
            return [
                'valid' => false,
                'reason' => self::ERROR_COMMENTS_NOT_ALLOWED,
            ];
        }

        return ['valid' => true, 'reason' => null];
    }
}
