<?php

namespace Zahzah\ModuleTransaction\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Zahzah\LaravelSupport\Contracts\DataManagement;

interface PaymentHistory extends DataManagement
{
    public function showUsingRelation() : array;
    public function getPaymentHistory(): mixed;
    public function prepareStorePaymentHistory(? array $attributes = null): Model;
    public function paymentHistory(mixed $conditionals = null): Builder;
    public function prepareShowPaymentHistory(? array $attributes = null): Model;
    public function showPaymentHistory(? Model $model = null): array;
    
}