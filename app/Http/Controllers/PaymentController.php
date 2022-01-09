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
class PaymentController extends Controller
{
    public function xeropdf(Request $request)
    {
        $order = Invoices::where('magic_link',$request->magiclink)->first();
        $pdf = getxeroinvoice($order->xero_id);

        return view('payment.pdf')->with(compact('pdf'));
    }
    public function payment(Request $request)
    {
        $invoice = Invoices::where('magic_link',$request->url)->first();
        return view('payment')->with('invoice',$invoice);

    }
    public function payReturn(Request $request , $token , $status)
    {
        $returns  = $request->all();
        GoSell::setPrivateKey(config('app.TAPPAYMENT_SecretAPIKey'));
        $returns = GoSell\Charges::retrieve($returns['tap_id']);
//dd($returns);
        $invoice_id = 0 ;
         if( $request->payment == 'invoice')
        {
             $invoice_id = $token;
            $lastTransacations = PaymentTransaction::where('order_id', $token)->where('status','CAPTURED')->get();
            $perviousAmount = 0 ;
            foreach ($lastTransacations as $lastTransacation)
            {
                $perviousAmount+=$lastTransacation->amount;
            }
            $invoice = Invoices::where('id',$token)->first();
            $orderstatus = Invoices::INVOICE_PARTIALLY_PAID ;

            if(($returns->amount+$perviousAmount) >= $invoice->amount)
            {
                $orderstatus = Invoices::INVOICE_PAID ;
            }

            foreach ($invoice->orders as $order )
            {
                if($returns->status=='CAPTURED' && $orderstatus == Invoices::INVOICE_PAID) {
                    $order->is_paid = 1;
                    $order->partially_paid = Orders::Fullpaid;
                    $order->save();
                }
                else
                {
                    $order->partially_paid = Orders::Partiallypaid;
                }
            }
            if($returns->status=='CAPTURED' && $orderstatus == Invoices::INVOICE_PAID ) {

                 $invoice->is_paid = 1;
                $invoice->save();
            }
            if($returns->status=='CAPTURED' )
            {
                try
                {
                    (addpaymentxero( $invoice->id ,0 , $returns->amount , config('app.XEROKNET')));
                }
                catch (\Exception $exception){}
                $transaction =  PaymentTransaction::create(
                    ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                        'refernece_number'=>$returns->reference->track
                        ,'amount'=>$returns->amount
                        ,'status'=>$returns->status
                        ,'invoice_id'=>$invoice_id
                        ,'date'=>Carbon::now(),
                        'response'=>json_encode($returns)]);
            }
            if($returns->status=='CAPTURED' ) {
                $pdf = getxeroinvoice($order->xero_id);

                return view('payment.pdf')->with(compact('pdf'));
            }
        }
        else
        {

            $order = Orders::find($token);
              if($returns->status=='CAPTURED') {
                $order->is_paid = 1;
                $order->save();
                try
                {
                     (addpaymentxero( $order->id ,1 , $returns->amount , config('app.XEROKNET')));
                }
                catch (\Exception $exception){}
                $transaction =  PaymentTransaction::create(
                    ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                        'refernece_number'=>$returns->reference->track
                        ,'amount'=>$returns->amount
                        ,'status'=>$returns->status
//                        ,'invoice_id'=>$invoice_id
                        ,'date'=>Carbon::now(),
                        'response'=>json_encode($returns)]);
                if($returns->status=='CAPTURED' ) {
                      $pdf = getxeroinvoice($order->xero_id);
                      return view('payment.pdf')->with(compact('pdf'));
                  }
                  else
                  {
                      $transaction =  PaymentTransaction::create(
                          ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                              'refernece_number'=>$returns->reference->track
                              ,'amount'=>$returns->amount
                              ,'status'=>$returns->status
//                        ,'invoice_id'=>$invoice_id
                              ,'date'=>Carbon::now(),
                              'response'=>json_encode($returns)]);
                      return redirect($order->payment_link)->with('alert', 'عملية الدفع لم تتم!');

                  }

            }
              else
              {
                  $transaction =  PaymentTransaction::create(
                      ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                          'refernece_number'=>$returns->reference->track
                          ,'amount'=>$returns->amount
                          ,'status'=>$returns->status
//                        ,'invoice_id'=>$invoice_id
                          ,'date'=>Carbon::now(),
                          'response'=>json_encode($returns)]);
                  return redirect($order->payment_link)->with('alert', 'عملية الدفع لم تتم!');

              }
        }

        $transaction =  PaymentTransaction::create(
            ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                'refernece_number'=>$returns->reference->track
                ,'amount'=>$returns->amount
                ,'status'=>$returns->status
                ,'invoice_id'=>$invoice_id
                ,'date'=>Carbon::now(),
                'response'=>json_encode($returns)]

        );
        $pdf = getxeroinvoice($order->xero_id);
        return view('payment.pdf')->with(compact('pdf'));

    }


    public function tapreturn(Request $request)
    {

    }
    public function payInvoice(Request $request)
    {


        $invoice = Invoices::where('magic_link',$request->id)->first();
        $transaction = null;
         $lastTransacations = PaymentTransaction::where('invoice_id', $invoice->id)->where('status','CAPTURED')->get();

         $perviousAmount = 0 ;
        foreach ($lastTransacations as $lastTransacation)
        {
            $perviousAmount+=$lastTransacation->amount;
        }
        if(!$invoice->is_paid)
        {
            return view('payment.index')->with(compact('invoice','transaction','perviousAmount'));
        }
        else
        {
            $pdf = getxeroinvoice($invoice->xero_id);
            return view('payment.pdf')->with(compact('pdf'));
        }

    }
    public function payOrderInvoice(Request $request)
    {


        $invoice = Orders::where('url',$request->url)->first();
        if(!$invoice)
        {
            return ;
        }
        $transaction = null ;
        if(!$invoice->is_paid)
        {
            return view('payment.order.index')->with(compact('invoice','transaction'));

        }
        else
        {
            $pdf = getxeroinvoice($invoice->xero_id);
            return view('payment.pdf')->with(compact('pdf'));
        }

    }
    public function makePayment(Request $request)
    {
        $invoice = Invoices::find($request->invoice_id);
        $data = tapmulitplepayment($request->amount,$invoice);
        return redirect($data);
    }
    public function makeOrderPayment(Request $request)
    {
        $invoice = Orders::find($request->order_id);

        $data = tappayment($invoice);
         return redirect($data);

    }
}