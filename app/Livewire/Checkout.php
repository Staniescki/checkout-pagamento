<?php

namespace App\Livewire;

use App\Enums\CheckoutStepsEnum;
use App\Services\CheckoutService;
use Livewire\Component;

class Checkout extends Component
{

    public array $cart = [];

    public int $step = CheckoutStepsEnum::INFORMATION->value;

    public function mount(CheckoutService $checkoutService)
    {
        $this->cart = $checkoutService->loadCart();

    }
    public function render()
    {
        return view('livewire.checkout');
    }
}
