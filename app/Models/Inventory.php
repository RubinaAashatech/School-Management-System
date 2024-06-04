<?php

namespace App\Models;

use App\Models\InventoryHead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = [
        'school_id',
        'inventory_head_id',
        'name',
        'invoice_number',
        'date',
        'amount',
        'description',
        'document',
        'is_active',
    ];
    public function incomeHead()
    {
        return $this->belongsTo(InventoryHead::class, 'inventory_head_id');
    }
}
