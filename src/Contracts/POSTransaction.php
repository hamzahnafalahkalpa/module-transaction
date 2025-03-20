<?php

namespace Hanafalah\ModuleTransaction\Contracts;

use Illuminate\Database\Eloquent\Model;

interface POSTransaction extends Transaction
{
    public function prepareCheckout(?array $attributes = null): Model;
    public function checkout(): array;
    public function prepareStorePaidPayment(?array $attributes = null): Model;
    public function storePaidPayment(): array;
}
