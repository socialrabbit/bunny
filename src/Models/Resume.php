<?php

namespace Bunny\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Resume extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'highlights',
        'file_path',
        'file_type',
        'alternate_formats',
        'is_active',
        'downloads',
    ];

    protected $casts = [
        'highlights' => 'array',
        'alternate_formats' => 'array',
        'is_active' => 'boolean',
        'downloads' => 'integer',
    ];

    /**
     * Get the download URL for the resume.
     */
    public function getDownloadUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the formatted file size.
     */
    public function getFileSizeAttribute()
    {
        return Storage::exists($this->file_path)
            ? $this->formatBytes(Storage::size($this->file_path))
            : null;
    }

    /**
     * Track a download of the resume.
     */
    public function trackDownload()
    {
        $this->increment('downloads');
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }

    /**
     * Scope a query to only include active resumes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 