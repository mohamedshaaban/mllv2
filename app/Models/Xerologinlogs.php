<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Xerologinlogs extends Model
{
    use CrudTrait;

    protected $table='xerologinlogs';
    protected $fillable = ['refresh_token','access_token'];
    /*
     *
     */
}