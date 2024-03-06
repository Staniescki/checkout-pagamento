<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Exceptions\PaymentException;
use App\Models\Order;
use Database\Seeders\OrderSeeder;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Payment\Payer;


Class CheckoutService
{

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('payment.mercadopago.access_token'));
    }

    public function loadCart(): array
    {
        $cart = Order::with('skus.product', 'skus.features')
            ->where('status', OrderStatusEnum::CART)
        ->where(function ($query) {
            $query->where('session_id', session()->getId());
            if (auth()->check()) {
                $query->orWhere('user_id', auth()->user()->id);
            }
        })->first();

        if (is_null($cart) && config('app.env') == 'local') {
            $seed = new OrderSeeder();
            $seed->run(session()->getId());
            return $this->loadCart();
        }

        return $cart->toArray();
    }

    public function creditCardPayment($data)
    {
            $code = random_int(100,500);

            $client = new PaymentClient();
            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(["X-Idempotency-Key: {$code}}" ]);

            $payment = $client->create([
                "transaction_amount" => (float) $data['transaction_amount'],
                "token" => $data['token'],
                "description" => $data['description'],
                "installments" => $data['installments'],
                "payment_method_id" => $data['payment_method_id'],
                "issuer_id" => $data['issuer_id'],
                "payer" => [
                    "email" => $data['payer']['email'],
                    "identification" => [
                        "type" => $data['payer']['identification']['type'],
                        "number" => $data['payer']['identification']['number']
                    ]
                ]
            ], $request_options);

            throw_if(!$payment->id || $payment->status === 'rejected',
                PaymentException::class,
                $payment->error?->message ?? "Verifique os dados do cartão");

            return $payment;

    }

    public function pixOrBankSlipPayment($data)
    {

        $code = random_int(100,500);

        $client = new PaymentClient();
        $request_options = new RequestOptions();
        $request_options->setCustomHeaders(["X-Idempotency-Key: {$code}"]);

        $payment = $client->create([
            "transaction_amount" => (float) $data['amount'],
            "payment_method_id" => $data['method'],
            "payer" => [
                "email" =>  config('payment.mercadopago.buyer_email'),
            ]
        ], $request_options);

        throw_if(!$payment->id || $payment->status === 'rejected',
            PaymentException::class,
            $payment->error?->message ?? "Verifique os dados informados");

        return $payment;

    }




}




;
