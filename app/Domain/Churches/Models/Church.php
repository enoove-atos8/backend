<?php

namespace Domain\Churches\Models;

use Illuminate\Database\Eloquent\Model;

class Church extends Model
{
    protected $table = 'churches';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'activated',
        'doc_type',
        'doc_number',
        'admin_email_tenant',
        'pass_admin_email_tenant',
    ];
}
