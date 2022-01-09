<?php

use App\User;
use TapPayments\GoSell;use App\Models\Orders;

if ( ! function_exists( 'xeroquotes' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $id
     *
     * @return  float|integer
     */
    function xeroquotes( $data ,$isOrder) {
        generatexerotoken();

        $lineItems = [] ;
        if($isOrder)
        {
            $order = \App\Models\Orders::find($data);
            $driver = User::where('id', $order->driver_id)->first();
            $acccount = isset($driver)? $driver->xero_account : null ;
            if(!$acccount)
            {
                $acccount  = ($order->payment_type == Orders::KNET_PAYMENT) ? config('app.XEROKNET'): config('app.XEROCASH');
            }

            if($order->payment_type == Orders::KNET_PAYMENT)
            {
                $account = config('app.XEROKNET');
            }
            if($order->areafrom)
            {
                $lineItems[] = [ "Description"=> '  Date:'.@$order->date.' , From : '. @$order->areafrom->name_en.' ,  To : '.@$order->areato->name_en.',   Driver : '.@$order->driver->name.',  Car Plate ID:'.@$order->cars->car_plate_id.',  Car Make:'.@$order->carmakes->name_en, "Quantity"=> "1", "UnitAmount"=> $order->amount?$order->amount:0, "AccountCode"=> '200', "TaxType"=> "NONE", "LineAmount"=> $order->amount?$order->amount:0 ];
            }
            else
            {
                $lineItems[] = [ "Description"=> '  Date:'.@$order->date.' , Driver : '.@$order->driver->name.',  Car Plate ID:'.@$order->cars->car_plate_id.',  Car Make:'.@$order->carmakes->name_en, "Quantity"=> "1", "UnitAmount"=> $order->amount?$order->amount:0, "AccountCode"=>'200' , "TaxType"=> "NONE", "LineAmount"=> $order->amount?$order->amount:0 ];

            }
        }
        else
        {
            $order = \App\Models\Invoices::find($data);
            foreach ($order->orders as $order)
            {
                try {

                    voidxeroinvoice($order->id);
                }
                catch (Exception $ex){}

                if($order->areafrom)
                {
                    $driver = User::where('id', $order->driver_id)->first();
                    $acccount = isset($driver)? $driver->xero_account : null ;
                    if(!$acccount)
                    {
                        $acccount  = ($order->payment_type == Orders::KNET_PAYMENT) ? config('app.XEROKNET'): config('app.XEROCASH');
                    }
                    if($order->payment_type == Orders::KNET_PAYMENT)
                    {
                        $account = config('app.XEROKNET');
                    }
                    $lineItems[] = [ "Description"=> '  Date:'.@$order->date.' ,  From : '. @$order->areafrom->name_en.' ,  To : '.@$order->areato->name_en.' ,  Driver : '.@$order->driver->name.',  Car Plate ID:'.@$order->cars->car_plate_id.',  Car Make:'.@$order->carmakes->name_en, "Quantity"=> "1", "UnitAmount"=> $order->amount?$order->amount:0, "AccountCode"=> '200', "TaxType"=> "NONE", "LineAmount"=> $order->amount?$order->amount:0 ];
                }
                else
                {
                    $lineItems[] = [ "Description"=> '  Date:'.@$order->date.' , Driver : '.@$order->driver->name.',  Car Plate ID:'.@$order->cars->car_plate_id.',  Car Make:'.@$order->carmakes->name_en, "Quantity"=> "1", "UnitAmount"=> $order->amount?$order->amount:0, "AccountCode"=> '200', "TaxType"=> "NONE", "LineAmount"=> $order->amount?$order->amount:0 ];

                }
            }
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.xero.com/api.xro/2.0/Invoices',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{ "Invoices": [ { "Type": "ACCREC", "Contact": {  "Name": "'.$order->customers->mobile.' - '.$order->customers->name.'"}, "LineItems":  '.json_encode($lineItems).' , "Date": "'.\Carbon\Carbon::today()->format('Y-m-d').'", "DueDate": "'.\Carbon\Carbon::today()->format('Y-m-d').'", "Reference": "WBMll-'.$order->id.'-'.$order->customers->mobile.'", "Status": "DRAFT" } ] }',
            CURLOPT_HTTPHEADER => array(
                'xero-tenant-id: '.config('app.XERO_TENANT_ID'),
                'Authorization: Bearer '.session('xero_token'),
                'Accept: application/json',
                'Content-Type: application/json',
                'Cookie: _abck=3BE5A6A05BAF5AB8553BA77E30FB8A3F~-1~YAAQnapkXzAvk2N7AQAAORektAYRzy1/LKPj39wbHeduxuuhTgQqQWjHTFBGaFIfXwPiMGD4hSFXFNlSHYV+IAYqvi21kA5YS3/fnu9afTE69mgLnoCtNAQdLhQoIo72RnZ0tLr60szlvZs3eDJ/VuzfICrss3Q0uTfkAqkvpttLZXw1wqz9D4T9sqBKMh6diuIt1c3bjqblM+ZRlP4/SaqyXZLYIp34Bnq94eRPiwIzEiljG5C24UKYYRUQ4MHX52AbW8eZVFLsyqbAn0Qn1lj4LSmkkxhem20wXVaIneuJmrOBYMgOo+QKWCasWo6Uk8IKTNdyb2vMJV40LlCbXej+ZRWjhLlhe86a9ixiIUQHsJ4pn061MYTLnB/WHdV3bdJQtxY=~-1~-1~-1; ak_bmsc=C70C0DC1D588A1C8CEEAC75CDDD8546B~000000000000000000000000000000~YAAQnapkX+LylGN7AQAA98REtQ0HVaP7WLJXq8WbcAbTAz5Ttc5sUtwlB14QXSuEyAZr8hiDZ6e+Cvj95P8omgjHzXNC+QKtFX+UlZaIrSHR7wRyz82/OPzRioAVeoU327akTPu7NM6bxmotyKMHGg2bsj9sbcGsuYtfygw9hH3MbxKUnzAyAfooSHvs5PIv516R/mxffwADhCaQAXBnUMSNN/MjcREAllZOPjr5buOVuFO7v6ckH3QQOu2Hx/rDV1SpWRyHATB+Z2KBh9s05GjefgO30HsGZwLD6YnQeldY7rr10M3+4NtvanO6PyYMgtGodQ7ySE83RPnDOydAGiX8b3MZ8yi6HNFo7NZ5VYiYSiMmtPTgWvs=; bm_sz=F3E197CF35509D745E364DF07E089962~YAAQnapkXy8vk2N7AQAAORektA1LcoR92ygBnq7W/AMkk35+XEmGpx9PB7dNw0lhE+p2B92FJRShAGU9mqX7rokkHEWL/jSSb/PTDKuIEVrlkkJ8cNWfFn/DHEEQ2OUivm0ZVAgECgoToph6cO2jB/1+WA9AIWCPpM1qHC+Vll0HeUqxwhbBwjwKI6Nhxg=='
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        if(isset($response->Title) && $response->Title =='Unauthorized')
        {
            generatexerotoken();
            return xeroquotes( $data ,$isOrder);

        }
        if($isOrder)
        {
            $order = \App\Models\Orders::find($data)->update(['xero_id'=>$response->Invoices[0]->InvoiceID]);
        }
        else
        {
            $invoice = \App\Models\Invoices::find($data);
            foreach ($invoice->orders as $order)
            {
                $order->update(['xero_id'=>$response->Invoices[0]->InvoiceID]);
            }
            $invoice->update(['xero_id'=>$response->Invoices[0]->InvoiceID]);

        }
    }

























}
if ( ! function_exists( 'xeroquotestoinvoice' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $id
     *
     * @return  float|integer
     */
    function xeroquotestoinvoice( $data ,$isOrder) {
        generatexerotoken();

        $lineItems = [] ;

            $order = \App\Models\Orders::find($data);
        $driver = User::where('id', $order->driver_id)->first();
        $total = 0 ;
        $acccount = isset($driver)? $driver->xero_account : null ;
        if(!$acccount)
        {
            $acccount  = ($order->payment_type == Orders::KNET_PAYMENT) ? config('app.XEROKNET'): config('app.XEROCASH');
        }
        if($order->payment_type == Orders::KNET_PAYMENT)
        {
            $account = config('app.XEROKNET');
        }
            if($order->areafrom)
            {
                $lineItems[] = [ "Description"=> '  Date:'.@$order->date.' ,  From : '. @$order->areafrom->name_en.' ,  To : '.@$order->areato->name_en.' ,  Driver : '.@$order->driver->name.',  Car Plate ID:'.@$order->cars->car_plate_id.',  Car Make:'.@$order->carmakes->name_en, "Quantity"=> "1", "UnitAmount"=> $order->amount, "AccountCode"=> '200', "TaxType"=> "NONE", "LineAmount"=> $order->amount ];
            }
            else
            {
                $lineItems[] = [ "Description"=> '  Date:'.@$order->date.' ,  From : '. @$order->areafrom->name_en.' ,  To : '.@$order->areato->name_en.' ,  Driver : '.@$order->driver->name.',  Car Plate ID:'.@$order->cars->car_plate_id.',  Car Make:'.@$order->carmakes->name_en, "Quantity"=> "1", "UnitAmount"=> $order->amount, "AccountCode"=> '200', "TaxType"=> "NONE", "LineAmount"=> $order->amount ];

            }
            if( $order->amount < 1 ) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.xero.com/api.xro/2.0/Invoices/' . $order->xero_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{ "Invoices": [ { "Type": "ACCREC", "Contact": { "Name": "' . $order->customers->mobile . ' - ' . $order->customers->name . '" }, "LineItems":  ' . json_encode($lineItems) . ' , "Date": "' . \Carbon\Carbon::today()->format('Y-m-d') . '", "DueDate": "' . \Carbon\Carbon::today()->format('Y-m-d') . '", "Reference": "WBMll-' . $order->id . '-' . $order->customers->mobile . '", "Status": "DRAFT" } ] }',
                    CURLOPT_HTTPHEADER => array(
                        'xero-tenant-id: ' . config('app.XERO_TENANT_ID'),
                        'Authorization: Bearer ' . session('xero_token'),
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Cookie: _abck=3BE5A6A05BAF5AB8553BA77E30FB8A3F~-1~YAAQnapkXzAvk2N7AQAAORektAYRzy1/LKPj39wbHeduxuuhTgQqQWjHTFBGaFIfXwPiMGD4hSFXFNlSHYV+IAYqvi21kA5YS3/fnu9afTE69mgLnoCtNAQdLhQoIo72RnZ0tLr60szlvZs3eDJ/VuzfICrss3Q0uTfkAqkvpttLZXw1wqz9D4T9sqBKMh6diuIt1c3bjqblM+ZRlP4/SaqyXZLYIp34Bnq94eRPiwIzEiljG5C24UKYYRUQ4MHX52AbW8eZVFLsyqbAn0Qn1lj4LSmkkxhem20wXVaIneuJmrOBYMgOo+QKWCasWo6Uk8IKTNdyb2vMJV40LlCbXej+ZRWjhLlhe86a9ixiIUQHsJ4pn061MYTLnB/WHdV3bdJQtxY=~-1~-1~-1; ak_bmsc=C70C0DC1D588A1C8CEEAC75CDDD8546B~000000000000000000000000000000~YAAQnapkX+LylGN7AQAA98REtQ0HVaP7WLJXq8WbcAbTAz5Ttc5sUtwlB14QXSuEyAZr8hiDZ6e+Cvj95P8omgjHzXNC+QKtFX+UlZaIrSHR7wRyz82/OPzRioAVeoU327akTPu7NM6bxmotyKMHGg2bsj9sbcGsuYtfygw9hH3MbxKUnzAyAfooSHvs5PIv516R/mxffwADhCaQAXBnUMSNN/MjcREAllZOPjr5buOVuFO7v6ckH3QQOu2Hx/rDV1SpWRyHATB+Z2KBh9s05GjefgO30HsGZwLD6YnQeldY7rr10M3+4NtvanO6PyYMgtGodQ7ySE83RPnDOydAGiX8b3MZ8yi6HNFo7NZ5VYiYSiMmtPTgWvs=; bm_sz=F3E197CF35509D745E364DF07E089962~YAAQnapkXy8vk2N7AQAAORektA1LcoR92ygBnq7W/AMkk35+XEmGpx9PB7dNw0lhE+p2B92FJRShAGU9mqX7rokkHEWL/jSSb/PTDKuIEVrlkkJ8cNWfFn/DHEEQ2OUivm0ZVAgECgoToph6cO2jB/1+WA9AIWCPpM1qHC+Vll0HeUqxwhbBwjwKI6Nhxg=='
                    ),
                ));

            }
            else
            {
                 $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.xero.com/api.xro/2.0/Invoices/' . $order->xero_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{ "Invoices": [ { "Type": "ACCREC", "Contact": { "Name": "' . $order->customers->mobile . ' - ' . $order->customers->name . '" }, "LineItems":  ' . json_encode($lineItems) . ' , "Date": "' . \Carbon\Carbon::today()->format('Y-m-d') . '", "DueDate": "' . \Carbon\Carbon::today()->format('Y-m-d') . '", "Reference": "WBMll-' . $order->id . '-' . $order->customers->mobile . '", "Status": "AUTHORISED" } ] }',
                    CURLOPT_HTTPHEADER => array(
                        'xero-tenant-id: ' . config('app.XERO_TENANT_ID'),
                        'Authorization: Bearer ' . session('xero_token'),
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Cookie: _abck=3BE5A6A05BAF5AB8553BA77E30FB8A3F~-1~YAAQnapkXzAvk2N7AQAAORektAYRzy1/LKPj39wbHeduxuuhTgQqQWjHTFBGaFIfXwPiMGD4hSFXFNlSHYV+IAYqvi21kA5YS3/fnu9afTE69mgLnoCtNAQdLhQoIo72RnZ0tLr60szlvZs3eDJ/VuzfICrss3Q0uTfkAqkvpttLZXw1wqz9D4T9sqBKMh6diuIt1c3bjqblM+ZRlP4/SaqyXZLYIp34Bnq94eRPiwIzEiljG5C24UKYYRUQ4MHX52AbW8eZVFLsyqbAn0Qn1lj4LSmkkxhem20wXVaIneuJmrOBYMgOo+QKWCasWo6Uk8IKTNdyb2vMJV40LlCbXej+ZRWjhLlhe86a9ixiIUQHsJ4pn061MYTLnB/WHdV3bdJQtxY=~-1~-1~-1; ak_bmsc=C70C0DC1D588A1C8CEEAC75CDDD8546B~000000000000000000000000000000~YAAQnapkX+LylGN7AQAA98REtQ0HVaP7WLJXq8WbcAbTAz5Ttc5sUtwlB14QXSuEyAZr8hiDZ6e+Cvj95P8omgjHzXNC+QKtFX+UlZaIrSHR7wRyz82/OPzRioAVeoU327akTPu7NM6bxmotyKMHGg2bsj9sbcGsuYtfygw9hH3MbxKUnzAyAfooSHvs5PIv516R/mxffwADhCaQAXBnUMSNN/MjcREAllZOPjr5buOVuFO7v6ckH3QQOu2Hx/rDV1SpWRyHATB+Z2KBh9s05GjefgO30HsGZwLD6YnQeldY7rr10M3+4NtvanO6PyYMgtGodQ7ySE83RPnDOydAGiX8b3MZ8yi6HNFo7NZ5VYiYSiMmtPTgWvs=; bm_sz=F3E197CF35509D745E364DF07E089962~YAAQnapkXy8vk2N7AQAAORektA1LcoR92ygBnq7W/AMkk35+XEmGpx9PB7dNw0lhE+p2B92FJRShAGU9mqX7rokkHEWL/jSSb/PTDKuIEVrlkkJ8cNWfFn/DHEEQ2OUivm0ZVAgECgoToph6cO2jB/1+WA9AIWCPpM1qHC+Vll0HeUqxwhbBwjwKI6Nhxg=='
                    ),
                ));

            }
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

    }

























}
