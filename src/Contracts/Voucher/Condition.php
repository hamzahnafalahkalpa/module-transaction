<?php

namespace Zahzah\ModuleTransaction\Contracts\Voucher;

use Illuminate\Database\Eloquent\Model;
use Zahzah\LaravelSupport\Contracts\DataManagement;

interface Condition extends DataManagement{
    public function validation(Model &$voucher,array &$attributes) : array;
}
