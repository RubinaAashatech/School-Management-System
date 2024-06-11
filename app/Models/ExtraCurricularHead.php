<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraCurricularHead extends Model
{
    use HasFactory;
    protected $table = 'extra_curricular_heads';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];
    public function inventories()
    {
        return $this->hasMany(Income::class, 'extra_curricular_heads_id');
    }
}
