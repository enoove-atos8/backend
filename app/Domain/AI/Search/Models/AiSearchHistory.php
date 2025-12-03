<?php

namespace App\Domain\AI\Search\Models;

use Illuminate\Database\Eloquent\Model;

class AiSearchHistory extends Model
{
    protected $table = 'ai_search_history';

    protected $fillable = [
        'user_id',
        'question',
        'sql_generated',
        'result_data',
        'result_title',
        'result_description',
        'suggested_followup',
        'execution_time_ms',
        'success',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'result_data' => 'array',
            'success' => 'boolean',
        ];
    }
}
