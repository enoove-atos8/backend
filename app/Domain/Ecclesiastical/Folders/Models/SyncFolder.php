<?php

namespace Domain\Ecclesiastical\Folders\Models;

use Illuminate\Database\Eloquent\Model;

class SyncFolder extends Model
{
    protected $table = 'sync_folders';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant',
        'ecclesiastical_divisions_group_id',
        'folder_id',
        'folder_name',
        'folder_devolution',
        'receipt_type',
        'entry_type',
        'exit_type',
    ];
}
