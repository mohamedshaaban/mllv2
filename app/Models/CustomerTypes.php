<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class CustomerTypes extends Model
{

    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'customer_types';
    protected $primaryKey = 'id';
    protected $fillable = ['name_en', 'name_ar'];

}