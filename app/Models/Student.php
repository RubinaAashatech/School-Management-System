<?php

namespace App\Models;

use App\Models\User;
use App\Models\Classg;
use App\Models\Section;
use App\Models\StudentLeave;
use App\Models\FeeCollection;
use App\Models\StudentSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'school_id', 'reservation_quota_id', 'admission_no', 'roll_no', 'admission_date', 'school_house_id', 'student_photo', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_phone', 'guardian_email', 'transfer_certificate', 'class_id', 'section_id'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function schoolHouse()
    {
        return $this->belongsTo(SchoolHouse::class, 'school_house_id');
    }
    public function classes()
    {
        return $this->belongsTo(Classg::class, 'class_id');
    }
    public function sections()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function studentLeaves()
    {
        return $this->hasMany(StudentLeave::class, 'student_id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'student_id');
    }


    public function session()
    {
        return $this->belongsTo(StudentSession::class);
    }
    public function feeCollections()
    {
        return $this->hasMany(FeeCollection::class);
    }
}
