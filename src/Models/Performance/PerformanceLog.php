<?php

namespace Bunny\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class PerformanceLog extends Model
{
    protected $fillable = [
        'response_time',
        'memory_usage',
        'cpu_usage',
        'disk_usage',
        'queue_size',
        'error_count'
    ];

    protected $casts = [
        'response_time' => 'float',
        'memory_usage' => 'float',
        'cpu_usage' => 'float',
        'disk_usage' => 'float',
        'queue_size' => 'integer',
        'error_count' => 'integer'
    ];
}
