<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface TransactionItem extends DataManagement
{
    public function addOrChange(?array $attributes = []): self;
    public function prepareShowTransactionItem(?Model $model = null): ?Model;
    public function showTransactionItem(?Model $model = null): array;
    public function prepareStoreTransactionItem(mixed $attributes = null): Model;
    public function storeTransactionItem(): array;
}
