<?php

use App\User;
use TapPayments\GoSell;
if ( ! function_exists( 'tapmulitplepayment' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $id
     *
     * @return  float|integer
     */
    function tapmulitplepayment($amount ,$data ) {


//set yout secret key here

        \TapPayments\GoSell::setPrivateKey(config('app.TAPPAYMENT_SecretAPIKey'));

        $order = \App\Models\Invoices::find( $data['id']);

        $charge = GoSell\Charges::create(
            [
                "amount"=> $amount,
                "currency"=> 'kwd',
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
                    "email"=> "tap@mllemergency.com",
                    "phone"=> [
                        "country_code"=> "965",
                        "number"=>  $order->customers->mobile
                    ]
                ],
                "source"=> [
                    "id"=> "src_all"
                ],
                "post"=> [
                    "url"=>route('paySuccess',[ $data['id'],'s','invoice'])
                ],
                "redirect"=> [
                    "url"=>route('paySuccess',[ $data['id'],'s','invoice'])
                ]
            ]
        );






        if(isset($charge->transaction)&& $charge->transaction->url)
        {
            return $charge->transaction->url;

        }
        return '';
    }

























}
