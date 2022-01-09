<?php

use App\User;
use TapPayments\GoSell;
use App\Models\Orders;
if ( ! function_exists( 'tappayment' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $id
     *
     * @return  float|integer
     */
    function tappayment( $data ) {


//set yout secret key here

        \TapPayments\GoSell::setPrivateKey(config('app.TAPPAYMENT_SecretAPIKey'));
        $order = \App\Models\Orders::find( $data['id']);


        $charge = GoSell\Charges::create(
            [
                "amount"=> $data['amount'],
                "currency"=> 'kwd',
                "description"=> ':  نوع السيارة'.@$order->carmakes->name_en.': من   '. @$order->areafrom->name_en.' ,   : الي '.@$order->areato->name_en,
                "threeDSecure"=> true,
                "save_card"=> false,
                "metadata"=> [
                    "udf1"=> "test 1",
                    "udf2"=> "test 2"
                ],
                "reference"=> [
                    "transaction"=> "txn_".$data['id'],
                    "order"=> $data['id']
                ],
                "receipt"=> [
                    "email"=> false,
                    "sms"=> true
                ],
                "customer"=> [
                    "first_name"=> $data['customerInfo'],
                    "middle_name"=> "",
                    "last_name"=> "",
                    "email"=> 'WBMll-'.$order->id.'-'.$order->customers->mobile,
                    "phone"=> [
                        "country_code"=> "965",
                        "number"=> $order->customers->mobile
                    ]
                ],
                "source"=> [
                    "id"=> "src_all"
                ],
                "post"=> [
                    "url"=>route('paySuccess',[ $data['id'],'s','order'])
                ],
                "redirect"=> [
                    "url"=>route('paySuccess',[ $data['id'],'s','order'])
                ]
            ]
        );






        if($charge->transaction->url)
        {
            return $charge->transaction->url;

        }
        return '';
    }

























}
