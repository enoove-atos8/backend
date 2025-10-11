<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountsFiles extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'account_id',
        'original_filename',
        'link',
        'file_type',
        'version',
        'reference_date',
        'status',
        'error_message',
        'deleted',
    ];
}
