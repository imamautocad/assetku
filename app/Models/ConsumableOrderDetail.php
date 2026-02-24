<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'consumable_order_details';

    protected $fillable = [
        'consumable_order_id',
        'no_req',
        'consumable_id',
        'category_id',
        'user_id',
        'qty',
        'status',
        'user_id',
    ];

    public function order()
    {
        return $this->belongsTo(ConsumableOrder::class, 'consumable_order_id', 'id');
    }

    // public function consumable() 
    // {
    //     return $this->belongsTo(Consumable::class, 'consumable_id');
    // }

    // public function category()
    // {
    //     return $this->belongsTo(Category::class, 'category_id');
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }
     public function consumable()
    {
          return $this->belongsTo(\App\Models\Consumable::class, 'consumable_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
