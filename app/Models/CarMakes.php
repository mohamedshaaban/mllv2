<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class CarMakes extends Model
{
    use CrudTrait;


    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'car_makes';
    protected $primaryKey = 'id';
    protected $fillable = ['name_en', 'name_ar'];

}