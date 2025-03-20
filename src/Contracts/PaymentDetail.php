<?php

namespace Zahzah\ModuleTransaction\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Zahzah\LaravelSupport\Contracts\DataManagement;

interface PaymentDetail extends DataManagement
{
    public function prepareStorePaymentDetail(? array $attributes = null): Model ;
    public function storePaymentDetail(): array;
    public function getPaymentDetail(): mixed;
    public function addOrChange(? array $attributes=[]): self;    
    public function paymentDetail(mixed $conditionals = null): Builder;
    public function get(mixed $conditionals = null): Collection;
    
}