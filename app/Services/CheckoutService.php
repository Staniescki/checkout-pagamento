<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Database\Seeders\OrderSeeder;

Class CheckoutService
{

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
            error_log('passei por aqui');
            $seed = new OrderSeeder();
            $seed->run(session()->getId());
            return $this->loadCart();
        }

        return $cart->toArray();
    }




}




;
