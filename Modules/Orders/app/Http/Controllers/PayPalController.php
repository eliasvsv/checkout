<?php

namespace Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Modules\Orders\Models\Orders;
use Illuminate\Support\Facades\Auth;

class PayPalController extends Controller
{   

    public function index()
    {
        return view('orders::checkout');
    }

    public function payment(Request $request)
    {
        $order = new Orders();
        $user = Auth::user();

        $order->user_id=$user->id;
        $order->status="pending";
        $order->total_amount=100;
        $order->save();
      
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $request->session()->put('orderId', $order->id);
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.payment.success'),
                "cancel_url" => route('paypal.payment/cancel'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "100.00"
                    ]
                ]
            ]
        ]);
        if (isset($response['id']) & $response['id'] != null) {
  
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
  
            return redirect()
                ->route('cancel.payment')
                ->with('error',  'Ha ocurrido un error');
  
        } else {
            return redirect()
                ->route('create.payment')
                ->with('error', $response['message'] ?? 'Ha ocurrido un error');
        }
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function paymentCancel(Request $request)
    {
        $orderId = $request->session()->get('orderId');
        $order = Orders::find($orderId);  
        $order->status= "Cancelled";
        $order->save();
        return redirect()
              ->route('paypal')
              ->with('error', $response['message'] ?? 'Ha cancelado la transaccion');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function paymentSuccess(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        $orderId = $request->session()->get('orderId');
        $order = Orders::find($orderId);
  
      
        if (isset($response['status']) & $response['status'] == 'COMPLETED') {
            $order->status= $response['status'];
            $order->save();
            return redirect()
                ->route('paypal')
                ->with('success', 'Transaccion completa');
        } else {
            $order->status= "Cancelled";
            $order->save();
            return redirect()
                ->route('paypal')
                ->with('error', $response['message'] ?? 'Ha ocurrido un error');
        }
    }

}
