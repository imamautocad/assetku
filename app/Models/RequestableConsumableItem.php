<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestableConsumableItem extends Model
{
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $fillable = [
        'request_id',
        'consumable_id',
        'quantity',
        'notes'
    ];

    public function requestable()
    {
        return $this->belongsTo(RequestableConsumable::class, 'request_id');
    }

    public function consumable()
    {
        return $this->belongsTo(\App\Models\Consumable::class, 'consumable_id');
    }
}
