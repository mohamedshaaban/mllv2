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
class HomeController extends Controller
{
    public function index(Request $request)
    {


        
      return  redirect('/admin');
    }
    public function xaerauth(Request $request)
    {
        session_start();

        $provider = new \Calcinai\OAuth2\Client\Provider\Xero([
            'clientId'          => '8920CEA30F9D43AAA1041890507ED411',
            'clientSecret'      => 'Pw3-sZIpCmtqPKjf8vgH88xtgcLSu1lvO3g1qM9ngoUEbeOO',
            'redirectUri'       => 'https://mll.techinvestkw.com/xero/auth/callback',
        ]);


        if (!isset($_GET['code'])) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl([
                'scope' => 'openid email profile accounting.transactions'
            ]);

            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: ' . $authUrl);
            exit;

// Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else {

            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);


            //If you added the openid/profile scopes you can access the authorizing user's identity.
            $identity = $provider->getResourceOwner($token);
//            print_r($identity);

            //Get the tenants that this user is authorized to access
            $tenants = $provider->getTenants($token);
//            print_r($tenants);
        }
        session(['xero_token'=>$token->getRefreshToken()]);
        generatexerotoken(true);
    }
}