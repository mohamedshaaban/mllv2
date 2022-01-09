<?php

use App\User;
use TapPayments\GoSell;
if ( ! function_exists( 'voidxeroinvoice' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $id
     *
     * @return  float|integer
     */
    function voidxeroinvoice( $data) {
        generatexerotoken();

            $order = \App\Models\Orders::find($data);
            $lineItems = [ "Description"=> @$order->areafrom->name_en.' '.@$order->areato->name_en, "Quantity"=> "1", "UnitAmount"=> 0, "AccountCode"=> "200", "TaxType"=> "NONE", "LineAmount"=> 0 ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.xero.com/api.xro/2.0/Invoices/'.$order->xero_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{ "Invoices": [ { "Status": "VOIDED" } ] }',
            CURLOPT_HTTPHEADER => array(
                'xero-tenant-id: '.config('app.XERO_TENANT_ID'),
                'Authorization: Bearer '.session('xero_token'),
                'Accept: application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        if(isset($response->Title) && $response->Title =='Unauthorized')
        {
            generatexerotoken();
            return  voidxeroinvoice( $data);

        }
 return $response;
        //             $order = \App\Models\Orders::find($data)->update(['xero_id'=>$response->Invoices[0]->InvoiceID]);

    }

























}
