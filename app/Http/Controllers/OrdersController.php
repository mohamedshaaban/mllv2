<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoices;
use App\Models\Orders;
use App\Models\PaymentTransaction;
use Backpack\NewsCRUD\app\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use TapPayments\Requests\Retrieve;
use TapPayments\GoSell;
class OrdersController extends Controller
{

    public function shareOrder(Request  $request)
    {
        \App::setLocale(session('locale'));

        $order = Orders::find($request->id);
        $text = '' ;
        $text.= trans('admin.Order Id').' : '.$order->invoice_unique_id.'%0A';
        $text.= trans('admin.Car Make').' : '.@$order->carmakes->name_en.'%0A';
        $text.= trans('admin.Car Model').' : '.@$order->carmodel->name_en.'%0A';
        $text.= trans('admin.Driver').' : '.@$order->driver->name.'%0A';
//            $text.= trans('admin.Status').' : '.@$this->requeststatus->name_en.'%0A';
//            $text.= trans('admin.Address').' : '.@$this->address.'%0A';
        $text.= trans('admin.Date').' : '.@$order->date.'%0A';
        $text.= trans('admin.Area From').' : '.@$order->areafrom->name_en.'%0A';
        $text.= trans('admin.Area To').' : '.@$order->areato->name_en.'%0A';
        $text.= trans('admin.remarks').' : '.@$order->remarks.'%0A';
        $text.= trans('admin.Amount').' : '.@$order->amount.'%0A';
 
        if($order->payment_link){$text.= trans('admin.Pay_Link').' : '.@$order->payment_link.'%0A';}

        return "https://wa.me/+965".$order->customers->mobile."/?text=".$text;
        
    }
    public function copyOrder(Request  $request)
    {
        \App::setLocale(session('locale'));

        $order = Orders::find($request->id);
        $text = '' ;
        $text.= trans('admin.Order Id').' : '.$order->invoice_unique_id.',';
        $text.= trans('admin.Car Make').' : '.@$order->carmakes->name_en.',';
        $text.= trans('admin.Car Model').' : '.@$order->carmodel->name_en.',';
        $text.= trans('admin.Driver').' : '.@$order->driver->name.',';
//            $text.= trans('admin.Status').' : '.@$this->requeststatus->name_en.',';
//            $text.= trans('admin.Address').' : '.@$this->address.',';
        $text.= trans('admin.Date').' : '.@$order->date.',';
        $text.= trans('admin.Area From').' : '.@$order->areafrom->name_en.',';
        $text.= trans('admin.Area To').' : '.@$order->areato->name_en.',';
        $text.= trans('admin.remarks').' : '.@$order->remarks.',';

        $text.= trans('admin.Amount').' : '.@$order->amount.',';

        if($order->payment_link){$text.= trans('admin.Pay_Link').' : '.@$order->payment_link.',';}


        return $text;

    }
}