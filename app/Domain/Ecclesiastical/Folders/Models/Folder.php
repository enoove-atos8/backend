<?php

namespace Domain\Ecclesiastical\Folders\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $table = 'google_drive_ecclesiastical_groups_folders';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ecclesiastical_divisions_group_id',
        'folder_id',
        'folder_name',
        'folder_devolution',
        'entry_type',
    ];
}
