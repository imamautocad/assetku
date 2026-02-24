<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestableConsumable extends Model
{
    // Jika migrasi pakai unsignedInteger id manual, pastikan $incrementing = true dan keyType sesuai
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'department_id',
        'no_request',
        'status',
        'notes'
    ];

    public function items()
    {
        return $this->hasMany(RequestableConsumableItem::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }
}
