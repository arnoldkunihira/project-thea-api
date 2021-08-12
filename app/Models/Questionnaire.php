<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * A questionnaire consists of many questions.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // /**
    //  * Get a questionnaire that belongs to a project.
    //  */
    // public function project()
    // {
    //     return $this->belongsTo(Project::class, 'project_id');
    // }

    // /**
    //  * Get a questionnaire that belongs to a subject.
    //  */
    // public function subject()
    // {
    //     return $this->belongsTo(Subject::class, 'subject_id');
    // }

    // /**
    //  * A questionnaire consists of many sample test trackers.
    //  */
    // public function sample_test_trackings()
    // {
    //     return $this->hasMany(SampleTestTracking::class);
    // }

    // /**
    //  * Get a questionnaire that belongs to a status.
    //  */
    // public function status()
    // {
    //     return $this->belongsTo(Status::class, 'status_id');
    // }
}
