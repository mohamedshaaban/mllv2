<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Invoices extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    const INVOICE_PAID = 10 ;
    const INVOICE_PARTIALLY_PAID = 9 ;


    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $appends = ['linktocopy','paid','share_link'];
    protected $fillable = ['is_paid', 'url','date_from','date_to','customer_id','xero_id','invoice_unique_id'];
    public function orders()
    {
        return $this->belongsToMany(Orders::class, 'order_invoices');
    }
    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }
    public function getLinktocopyAttribute($value)
    {
        if($this->attributes['magic_link'])
        {
            return URL::to('/pay').'/'.$this->attributes['magic_link'];
        }

    }
    public function getPaidAttribute()
    {
        if($this->is_paid)
        {
            return trans('admin.Paid');
        }
        return trans('admin.Not Paid');
    }
    public function openGoogle($crud = false)
    {

        return '<a class="btn btn-sm btn-link" target="_blank" href="/payxeroinvoice/'.$this->attributes['magic_link'].'"  ><i class="la la-search"></i>Xero Invoice</a>';


    }
    public function getShareLinkAttribute($crud = false)
    {
        if($this->customers)
        {
            $text = '' ;
            $text.= trans('admin.MLL EMERGENCY ROADSIDE ASSISTANCE').'%0A';
            $text.= trans('admin.Invoice Id').' : '.$this->invoice_unique_id.'%0A';

            $text.= trans('admin.Amount').' : '.@$this->amount.'%0A';
            $text.= trans('admin.Pay_Link').' : '.@URL::to('/pay/invoice').'/'.$this->attributes['magic_link'].'%0A';
            return '<a class="btn btn-sm btn-link" target="_blank" href="https://wa.me/+965'.$this->customers->mobile.'/?text='.($text).'"  ><i class="lab la-whatsapp"></i> Share</a>';
        }

    }

}