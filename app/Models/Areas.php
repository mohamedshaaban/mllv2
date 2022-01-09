<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'areas';
    protected $primaryKey = 'id';
    protected $fillable = ['name_en', 'name_ar'];
    protected $appends = ['name'];

    public function getNameAttribute()
    {
        if(session('locale')=='ar')
        {
            return $this->attributes['name_ar'];
        }
        return $this->attributes['name_en'];
    }
}