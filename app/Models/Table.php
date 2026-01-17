<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'cod',
        'capacity',
        'status',
        'zone',
        'shape',
        'min_capacity',
        'is_active',
    ];
}
