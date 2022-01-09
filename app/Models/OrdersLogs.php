<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class OrdersLogs extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    const CASH_PAYMENT = 1 ;
    const KNET_PAYMENT = 2;
    const LATE_PAYMENT = 3 ;

    protected $table = 'orders_logs';
    protected $primaryKey = 'id';
    protected $fillable = ['order_id', 'user_id','order_status_id'];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requeststatus()
    {
        return $this->belongsTo(RequestStatus::class, 'order_status_id');
    }
}