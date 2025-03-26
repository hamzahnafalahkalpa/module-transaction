<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface PaymentDetail extends DataManagement
{
    public function prepareStorePaymentDetail(?array $attributes = null): Model;
    public function storePaymentDetail(): array;
    public function getPaymentDetail(): mixed;
    public function addOrChange(?array $attributes = []): self;
    public function paymentDetail(mixed $conditionals = null): Builder;
    public function get(mixed $conditionals = null): Collection;
}
