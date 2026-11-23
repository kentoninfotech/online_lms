<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseContentResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_content_resources';

    protected $fillable = [
        'course_content_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'sequence',
        'is_published',
        'download_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'download_count' => 'integer',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the content this resource belongs to
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(CourseContent::class, 'course_content_id');
    }

    /**
     * Get the course through content
     */
    public function getCourse()
    {
        return $this->content?->course;
    }

    /**
     * Get the file URL
     */
    public function getFileUrl(): string
    {
        return asset($this->file_path);
    }

    /**
     * Get human-readable file size
     */
    public function getHumanReadableSize(): string
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $i < count($units) && $size >= 1024; $i++) {
            $size /= 1024;
        }
        
        return number_format($size, 2) . ' ' . $units[$i];
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Get icon class based on file type
     */
    public function getIconClass(): string
    {
        $icons = [
            'pdf' => 'bi-file-pdf',
            'doc' => 'bi-file-word',
            'docx' => 'bi-file-word',
            'xls' => 'bi-file-excel',
            'xlsx' => 'bi-file-excel',
            'ppt' => 'bi-file-ppt',
            'pptx' => 'bi-file-ppt',
            'zip' => 'bi-file-zip',
            'txt' => 'bi-file-text',
            'jpg' => 'bi-file-image',
            'jpeg' => 'bi-file-image',
            'png' => 'bi-file-image',
            'gif' => 'bi-file-image',
            'mp3' => 'bi-file-music',
            'mp4' => 'bi-file-video',
        ];

        return $icons[$this->file_type] ?? 'bi-file-earmark';
    }
}
