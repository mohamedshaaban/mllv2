<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cars extends Model
{
    use CrudTrait;
    use SoftDeletes;

    /*
   |--------------------------------------------------------------------------
   | GLOBAL VARIABLES
   |--------------------------------------------------------------------------
   */

    protected $table = 'cars';
    protected $primaryKey = 'id';
    protected $fillable = ['car_plate_id','customer_id'];
    protected $appends=['customername'];
    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function getCustomernameAttribute()
{
    if($this->customers)
    {
        return $this->customers->name;
    }
    return '--';
}


    public function getCartypesnameAttribute()
    {
        if($this->cartypes)
        {
            return $this->cartypes->name_en;
        }
        return '--';
    }
}