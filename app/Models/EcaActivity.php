<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcaActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'pdf_image',
        'player_type',
        'is_active',
        'school_id',
        'eca_head_id',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function ecaHead()
    {
        return $this->belongsTo(ExtraCurricularHead::class, 'eca_head_id');
    }
}
