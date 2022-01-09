<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class OrderInvoicess extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'order_invoices';
protected $fillable=['orders_id',
                'invoices_id'];
}