<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumablesUsers extends Model
{
    use HasFactory;

    // karena nama tabel bukan bentuk default laravel (plural dari model)
    protected $table = 'consumables_users';

    protected $fillable = [
        'created_by',
        'consumable_id',
        'assigned_to',
        'note',
    ];

    // Relasi opsional:
    public function consumable()
    {
        return $this->belongsTo(Consumable::class, 'consumable_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
