<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    protected $fillable = [
        'academic_session_id',
        'school_id',
        'class_id',
        'section_id',
        'subject_group_id',
        'subject_id',
        'lesson_id',
        'topic_name'
    ];

    public function subjectGroups()
    {
        return $this->belongsTo(SubjectGroup::class, 'subject_group_id', 'id');
    }
    public function subjects()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function classes()
    {
        return $this->belongsTo(Classg::class, 'class_id', 'id');
    }
    public function sections()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function lessons()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id', 'id');
    }
}