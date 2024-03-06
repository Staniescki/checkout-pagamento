<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Mail\PaymentApprovedMail;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;


class PaymentService
{

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('payment.mercadopago.access_token'));
    }

    public function update($external_id): void
    {

        $code = random_int(100,500);

        $mp_payment_class = new PaymentClient();
        $request_options = new RequestOptions();
        $request_options->setCustomHeaders(["X-Idempotency-Key: {$code}"]);

        $mp_payment = $mp_payment_class->get($external_id);

        $payment = Payment::with('order.user')->where('external_id', $external_id)->firstOrFail();

        $payment->status = PaymentStatusEnum::parse($mp_payment->status);
        $payment->save();

        if ($payment->status === PaymentStatusEnum::PAID) {
            $payment->approved_at = $mp_payment->date_approved;
            $payment->order->status = OrderStatusEnum::PAID;
            $payment->order->save();

            Mail::to($payment->order->user->email)->queue(new PaymentApprovedMail($payment->order));
        }

        if ($payment->status === PaymentStatusEnum::CANCELLED || $payment->status === PaymentStatusEnum::REJECTED)
        {
            $payment->order->status = OrderStatusEnum::parse($mp_payment->status);
            $payment->order->save();
        }

    }
}
