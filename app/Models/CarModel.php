<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarModel extends Model
{

    use CrudTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'car_model';
    protected $primaryKey = 'id';
    protected $fillable = ['name_en', 'name_ar','car_make'];

    public function carmakes()
    {
        return $this->belongsTo(CarMakes::class, 'car_make');
    }
}