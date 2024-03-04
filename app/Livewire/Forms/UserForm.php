<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    #[Rule('required|email')]
    public $email = "";

    #[Rule('required|min:3|max:255')]
    public $name = "";

}
