<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Orders extends Model
{
    use CrudTrait;
 use SoftDeletes;
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    const ORDER_NOT_PAID = 0 ;
    const ORDER_PAID = 1;
    const CASH_PAYMENT = 1 ;
    const KNET_PAYMENT = 2;
    const LATE_PAYMENT = 3 ;
    const COMPLETED_ORDER = 6 ;
    const Fullpaid = 0 ;
    const Partiallypaid = 1 ;
    protected $table = 'orders';
    protected $appends = ['comission_paid_status','paid_status','invoice_id','paymenttext'];
    protected $primaryKey = 'id';
    protected $fillable = ['invoice_unique_id', 'customer_id','car_id','area_from','area_to','driver_id','status','address','paid_by',
    'comission','comission_paid','commission_date_paid','date','time','amount','xero_id','payment_type','link_generated','is_paid','payment_link','url','order_collected','collected_date','car_make','car_model','partially_paid','remarks'];
    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }
    public function paidby()
    {
        return $this->belongsTo(Customers::class, 'paid_by');
    }
    public function cars()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }
    public function carsorders()
    {
        return $this->belongsTo(CarsOrders::class, 'car_id');
    }
    public function carmakes()
    {
        return $this->belongsTo(CarMakes::class, 'car_make'); 
    }
    public function carmodel()
    {
        return $this->belongsTo(CarModel::class, 'car_model');
    }
    public function areafrom()
    {
        return $this->belongsTo(Areas::class, 'area_from');
    }
    public function areato()
    {
        return $this->belongsTo(Areas::class, 'area_to');
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id')->whereNotNull('is_driver');
    }
    public function requeststatus()
    {
        return $this->belongsTo(RequestStatus::class, 'status');
    }
    public function getPaymenttextAttribute()
    {
        if($this->attributes['payment_type'] == Orders::KNET_PAYMENT)
        {
            return trans('admin.Knet');
        }
        return trans('admin.Cash');
    }
    public function getInvoiceIdAttribute()
    {
        $orderInovces = OrderInvoicess::where('orders_id', $this->attributes['id'])->first();
        if($orderInovces) {
            $invoice = Invoices::find($orderInovces->invoices_id);
            return $invoice->invoice_unique_id;
        }
        return $this->attributes['invoice_unique_id'];
    }
    public function invoices()
    {
        return $this->belongsToMany(Invoices::class, 'order_invoices');
    }
    public function getPaidStatusAttribute($value)
    {
         if($this->is_paid)
        {
            return trans('admin.Paid');
        }
        return trans('admin.Not Paid');
    }
    public function getComissionPaidStatusAttribute($value)
    {
         if($this->comission_paid)
        {
            return 'Paid';
        }
        return 'Not Paid';
    }
    public function createXero($crud = false)
    {
        return '<a class="btn btn-sm btn-link" target="_blank" href="'.route('createXeroIncoie',$this->id).'"  >Create Xero Invoce</a>';

    }
        public function openGoogle($crud = false)
    {
        if($this->customers)
        {
            $text = '';
            $text.= trans('admin.MLL EMERGENCY ROADSIDE ASSISTANCE').'%0A';
            $text.= trans('admin.Order Id').' : '.$this->invoice_unique_id.'%0A';
            $text.= trans('admin.Car Make').' : '.@$this->carmakes->name_en.'%0A';
            $text.= trans('admin.Car Model').' : '.@$this->carmodel->name_en.'%0A';
            $text.= trans('admin.Driver').' : '.@$this->driver->name.'%0A';
//            $text.= trans('admin.Status').' : '.@$this->requeststatus->name_en.'%0A';
//            $text.= trans('admin.Address').' : '.@$this->address.'%0A';
            $text.= trans('admin.Date').' : '.@$this->date.'%0A';
            $text.= trans('admin.Area From').' : '.@$this->areafrom->name_en.'%0A';
            $text.= trans('admin.Area To').' : '.@$this->areato->name_en.'%0A';
            $text.= trans('admin.remarks').' : '.@$this->remarks.'%0A';
            $text.= trans('admin.Amount').' : '.@$this->amount.'%0A';

            if($this->payment_link){$text.= trans('admin.Pay_Link').' : '.@$this->payment_link.'%0A';}
            return '<a class="btn btn-sm btn-link" target="_blank" href="https://wa.me/+965'.$this->customers->mobile.'/?text='.($text).'"  ><i class="lab la-whatsapp"></i>'.trans('admin.share').'</a>';
        }

    }
}