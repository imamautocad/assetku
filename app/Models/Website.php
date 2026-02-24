<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\WebsiteFile;
use Carbon\Carbon;

class Website extends Model
{
    protected $fillable = [
        'manufacturer_id',
        'category_id',
        'company_id',
        'decs',
        'id_subscribe', 
        'name',
        'period_subscribe',
        'status',
        'pay_date',
        'expired_date',
        'price',
        'user_id',
    ];

    protected $casts = [
        'pay_date' => 'date:Y-m-d',
        'expired_date' => 'date:Y-m-d',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(\App\Models\Manufacturer::class, 'manufacturer_id');
    }

    public function getDisplayNameAttribute()
    {
        return $this->decs ?? 'Website #' . $this->id;
    }

    public function present()
    {
        return new \App\Presenters\WebsitePresenter($this);
    }
    
    public static function getExpiringWebsites($interval)
    {
        return self::whereDate('expired_date', '<=', now()->addDays($interval))
        ->whereDate('expired_date', '>=', now())
        ->get();
    }

    public function files()
    {
        return $this->hasMany(WebsiteFile::class);
    }
    
    public function isRenewable()
    {
        if (!$this->expired_date) {
            return false;
        }

        return now()->greaterThanOrEqualTo(
            $this->expired_date->copy()->subDays(30)
        );
    }
}
 