<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CarsOrders extends Model
{
    use CrudTrait;
     /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'cars';
    protected $primaryKey = 'id';
    protected $fillable = ['car_plate_id'];



    public function getCartypesnameAttribute()
    {
        if($this->cartypes)
        {
            return $this->cartypes->name_en;
        }
        return '--';
    }
}