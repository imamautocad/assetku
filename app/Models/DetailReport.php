<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Watson\Validating\ValidatingTrait; 

class License extends Depreciable
{
    use HasFactory;

    protected $presenter = \App\Presenters\LicensePresenter::class;

    use SoftDeletes;
    use CompanyableTrait;
    use Loggable, Presentable;
    protected $injectUniqueIdentifier = true;
    use ValidatingTrait;

    // We set these as protected dates so that they will be easily accessible via Carbon

    public $timestamps = true;

    protected $guarded = 'id';
    protected $table = 'v_detail_asset';

    protected $casts = [
        'purchase_date' => 'datetime',
        'expiration_date' => 'datetime',
        'termination_date' => 'datetime',
        'category_id'  => 'integer',
        'company_id'   => 'integer',
    ];

    protected $rules = [
        'name'   => 'required|string|min:3|max:255',
        'seats'   => 'required|min:1|integer',
        'license_email'   => 'email|nullable|max:120',
        'license_name'   => 'string|nullable|max:100',
        'notes'   => 'string|nullable',
        'category_id' => 'required|exists:categories,id',
        'company_id' => 'integer|nullable',
        'purchase_cost'=> 'numeric|nullable|gte:0',
        'purchase_date'   => 'date_format:Y-m-d|nullable',
        'expiration_date'   => 'date_format:Y-m-d|nullable',
        'termination_date'   => 'date_format:Y-m-d|nullable',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company',
        'email',
        'model',
        'cpu',
        'ram',
        'sn',
        'asset_use',
    ];

    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableAttributes = [
        'company',
        'email',
        'model',
        'cpu',
        'ram',
        'sn',
        'asset_use',
    ];

    /**
     * The relations and their attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableRelations = [
      'manufacturer' => ['name'],
      'company'      => ['name'],
      'category'     => ['name'],
      'depreciation' => ['name'],
    ];

    /**
     * Update seat counts when the license is updated
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v3.0]
     */
    public static function boot()
    {
        parent::boot();
        // We need to listen for created for the initial setup so that we have a license ID.
        static::created(function ($license) {
            $newSeatCount = $license->getAttributes()['seats'];

            return static::adjustSeatCount($license, 0, $newSeatCount);
        });
        // However, we listen for updating to be able to prevent the edit if we cannot delete enough seats.
        static::updating(function ($license) {
            $newSeatCount = $license->getAttributes()['seats'];
            //$oldSeatCount = isset($license->getOriginal()['seats']) ? $license->getOriginal()['seats'] : 0;
            /*
               That previous method *did* mostly work, but if you ever managed to get your $license->seats value out of whack
               with your actual count of license_seats *records*, you would never manage to get back 'into whack'.
               The below method actually grabs a count of existing license_seats records, so it will be more accurate.
               This means that if your license_seats are out of whack, you can change the quantity and hit 'save' and it
               will manage to 'true up' and make your counts line up correctly.
            */
            $oldSeatCount = $license->license_seats_count;

            return static::adjustSeatCount($license, $oldSeatCount, $newSeatCount);
        });
    }

    /**
     * Balance seat counts
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v3.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
}
