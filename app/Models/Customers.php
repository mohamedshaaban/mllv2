<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use CrudTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

     const CUSTOMER = 1;
     const GARAGE = 2;

     const ACTIVE = 1;
     const BLOCK = 2;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'mobile','status','type'];
    protected $appends =['num_of_cars','num_of_orders','num_pending_of_orders','amt_pending_of_orders','amt_of_orders','num_paid_of_orders'];

    public function cars()
    {
        return $this->hasMany(Cars::class, 'customer_id');
    }
    public function orders()
    {
        return $this->hasMany(Orders::class, 'customer_id');
    }
    public function getNumOfCarsAttribute($value)
    {
        return $this->cars->count();
    }

    public function getNumOfOrdersAttribute($value)
    {
        return Orders::where('paid_by',$this->attributes['id'])->where('is_paid',1)->where('link_generated',0)->where('payment_type', Orders::KNET_PAYMENT)->where('status',6)->count();

        return $this->orders->count();

    }
    public function getNumPendingOfOrdersAttribute($value)
    {
        
        return Orders::where('paid_by',$this->attributes['id'])->where('is_paid', '!=' ,1)->where('payment_type', Orders::KNET_PAYMENT)->count();
    }
    public function getNumPaidOfOrdersAttribute($value)
    {

        return Orders::where('paid_by',$this->attributes['id'])->where('is_paid', 1)->where('payment_type', Orders::KNET_PAYMENT)->count();
    }

    public function getAmtPendingOfOrdersAttribute($value)
    {
         return Orders::where('paid_by',$this->id)->where('is_paid', '!=' ,1)->where('payment_type', Orders::KNET_PAYMENT)->where('status',6)->sum('amount');
    }
    public function getAmtOfOrdersAttribute($value)
    {

        return Orders::where('paid_by',$this->id)->where('is_paid', 1)->where('payment_type', Orders::KNET_PAYMENT)->sum('amount');
    }

    public function customertypes()
        {
            return $this->belongsTo(CustomerTypes::class, 'type');
        }

    }