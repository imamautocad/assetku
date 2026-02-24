<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumableOrder extends Model
{
    protected $table = 'consumable_orders';

    protected $fillable = [
        'no_req',
        'no_po',
        'user_id',
        'department_id',
        'notes',
        'status',
    ];

    // Relasi ke user (pembuat)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public function details()
    {
        return $this->hasMany(\App\Models\ConsumableOrderDetail::class, 'consumable_order_id');
    }
    

    /**
     * Scope : apply visibility rule for a given user
     * - jika user->canViewAllOrders() => tidak filter
     * - selain itu => hanya where user_id = $user->id
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('0 = 1'); // no user => return empty
        }

        if ($user->canViewAllOrders()) {
            return $query;
        }

        // End user sees only own requests
        return $query->where('user_id', $user->id);
    }
}
