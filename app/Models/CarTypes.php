<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class CarTypes extends Model
{

    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'car_types';
    protected $primaryKey = 'id';
    protected $fillable = ['name_en', 'name_ar','image'];

}