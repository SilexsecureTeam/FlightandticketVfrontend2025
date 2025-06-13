<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Payment;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PaystackController extends Controller
{
    use HttpResponses;

    public function initializePayment($OrderId, $email, $amount, $type)
    {
        try {
            $amountInKobo = (int)(floatval($amount) * 100);

                $timezone = 'Africa/Lagos';
       // Bad request



                $client = new Client();
                $response = $client->post("https://api.paystack.co/transaction/initialize", [
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('services.paystack.secret_key'),
                        'Content-Type' => 'application/json',
                        'Cache-Control' => 'no-cache',
                    ],
                    'json' => [
                        'amount' =>  $amountInKobo,
                        'email' =>$email,
                        // 'metadata' => [
                        //     'payment_type' => $paymentType,
                        // ],

                    ],
                ]);

                $data = json_decode($response->getBody(), true);
                $transaction = new Payment([
                    'flight_order_id' => $OrderId,
                    'email' => $email,
                    'amount' => $amount,
                    'provider' => 'paystack',
                    'payment_type' => $type,
                    'payment_status' => 'PENDING',
                    'payment_date' => now($timezone)->format('Y-m-d H:i:s'),
                    'reference' => $data['data']['reference'],
                ]);

                if ($transaction->save()) {
                    return response([
                        'payment_status' => 'Initialize',
                        'message' => $data['message'],
                        'paystackURL' => $data['data']['authorization_url'],
                        'payment_id' => $transaction->id,
                    ], 200);
                } else {
                    return response(
                        [
                            'status' => 'error',
                            'message' => 'Something went wrong',
                        ], 500
                    );
                }

        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    }
