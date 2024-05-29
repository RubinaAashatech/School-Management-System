<?php

namespace App\Models;

use App\Models\SubjectGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['subject_code', 'subject', 'credit_hour', 'is_active', 'school_id'];

    public function subjectGroups()
    {
        return $this->hasMany(SubjectGroup::class, 'subject_id');
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'subject_id');
    }
}
