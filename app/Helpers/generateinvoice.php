<?php

use App\Models\Invoices;
use App\Models\OrderInvoicess;
use App\Models\Orders;
use App\User;
use TapPayments\GoSell;
use Illuminate\Support\Facades\DB;
if ( ! function_exists( 'generateinvoice' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $data
     *
     * @return  float|integer
     */
    function generateinvoice( $data ) {
        generatexerotoken();
        $userID = $data['userid'];
        $ordersId = $data['orderid'];
        $total = 0 ;

        $orders = Orders::whereIn('id',$ordersId)
            ->get();
         $inveiceChk = OrderInvoicess::whereIn('orders_id',$ordersId)->first();
        if($inveiceChk)
        {
            return ;
        }
//        DB::unprepared('UNLOCK TABLES invoices WRITE');
        DB::raw('LOCK TABLES invoices WRITE');
        $lastID = Invoices::OrderBy('id','DESC')->first();
        $lastInvoiceId = 0 ;
        if($lastID)
        {
            $lastInvoiceId = $lastID->id;
        }
        $invoice = Invoices::create([
           'is_paid'=>0,
            'customer_id'=>$userID,
            'invoice_unique_id'=>'WBMLL-INV-'.($lastInvoiceId+1)

        ]);
        foreach($orders as $order)
        {
            try
            {
                (voidxeroinvoice($order->id));
            }
            catch (Exception $exception){}
            $order->link_generated =1 ;
            $order->save();
            $total+= $order->amount;
             OrderInvoicess::create([
                'orders_id'=>$order->id,
                'invoices_id'=>$invoice->id
            ]);
        }
          if($total>0)
        {
            $paymentLink =  tapmulitplepayment($total,$invoice);
            $invoice->url = $paymentLink;
            $invoice->amount = $total;
            $invoice->magic_link = generateRandomString(10);
            $invoice->update();
            foreach($orders as $order)
            {
                $order->payment_link = route('payInvoice',$invoice->magic_link);
                $order->save();
            }
        }
        else
        {
            $invoice = Invoices::whereId($invoice->id)->delete();
        }

        try
        {
             (xeroinvoice($invoice->id,0));
        }
        catch (\Exception $exception){}
        DB::raw('UNLOCK TABLES invoices WRITE');

    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

























}
