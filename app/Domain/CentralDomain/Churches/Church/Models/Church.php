<?php

namespace Domain\CentralDomain\Churches\Church\Models;

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
        'plan_id',
        'name',
        'activated',
        'doc_type',
        'doc_number',
        'aws_s3_bucket',
    ];
}
