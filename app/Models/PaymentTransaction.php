<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
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

    protected $table = 'payment_transaction';
    protected $primaryKey = 'id';
    protected $fillable = ['order_id', 'transaction_id','refernece_number','amount','status','date','response','invoice_id'];


}