<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLevel extends Model
{
    protected $fillable = ['name'];
    
    public $timestamps = false;
    
    /**
     * Get the levels
     */
    public static function getLevels()
    {
        return [
            'Local' => 'Local',
            'International' => 'International',
            'Diploma' => 'Diploma'
        ];
    }

    /**
     * Get all unique levels from courses
     */
    public static function getDistinctLevels()
    {
        return \App\Models\Course::distinct('level')->pluck('level')->sort()->values()->all();
    }
}
